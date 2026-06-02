<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class GoogleDriveService
{
    private Client $client;
    private Drive $drive;

    private const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

    public function __construct()
    {
        $clientId = trim((string) config('services.google_drive.client_id'));
        $clientSecret = trim((string) config('services.google_drive.client_secret'));
        $refreshToken = trim((string) config('services.google_drive.refresh_token'), " \t\n\r\0\x0B\"'");

        if (!$clientId || !$clientSecret || !$refreshToken) {
            throw new RuntimeException('Konfigurasi OAuth Google Drive belum lengkap di .env.');
        }

        $this->client = new Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->addScope(Drive::DRIVE);
        $this->client->setAccessType('offline');

        $accessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

        if (isset($accessToken['error'])) {
            throw new RuntimeException('Gagal refresh token Google Drive: ' . json_encode($accessToken));
        }

        $this->client->setAccessToken($accessToken);
        $this->drive = new Drive($this->client);
    }

    public function upload(UploadedFile $file, ?string $customFileName = null, ?string $folderId = null): array
    {
        $folderId = $folderId ?: config('services.google_drive.folder_id');

        if (!$folderId) {
            throw new RuntimeException('GOOGLE_DRIVE_FOLDER_ID belum diisi di .env.');
        }

        $fileName = $customFileName ?: $file->getClientOriginalName();

        $metadata = new DriveFile([
            'name' => $fileName,
            'parents' => [$folderId],
        ]);

        $uploadedFile = $this->drive->files->create($metadata, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getMimeType() ?: 'application/octet-stream',
            'uploadType' => 'multipart',
            'fields' => 'id,name,mimeType,size,webViewLink,webContentLink',
            'supportsAllDrives' => true,
        ]);

        return [
            'id' => $uploadedFile->id,
            'name' => $uploadedFile->name,
            'mime_type' => $uploadedFile->mimeType,
            'size' => $uploadedFile->size,
            'web_view_link' => $uploadedFile->webViewLink,
            'web_content_link' => $uploadedFile->webContentLink,
        ];
    }

    public function findOrCreateFolder(string $folderName, ?string $parentFolderId = null): string
    {
        $parentFolderId = $parentFolderId ?: config('services.google_drive.folder_id');

        if (!$parentFolderId) {
            throw new RuntimeException('Parent folder Google Drive belum diisi.');
        }

        $folderName = $this->sanitizeDriveName($folderName);
        $escapedFolderName = $this->escapeDriveQueryValue($folderName);

        $query = sprintf(
            "name = '%s' and mimeType = '%s' and '%s' in parents and trashed = false",
            $escapedFolderName,
            self::FOLDER_MIME_TYPE,
            $parentFolderId
        );

        $result = $this->drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id,name)',
            'spaces' => 'drive',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
        ]);

        $files = $result->getFiles();

        if (count($files) > 0) {
            return $files[0]->getId();
        }

        $metadata = new DriveFile([
            'name' => $folderName,
            'mimeType' => self::FOLDER_MIME_TYPE,
            'parents' => [$parentFolderId],
        ]);

        $folder = $this->drive->files->create($metadata, [
            'fields' => 'id,name',
            'supportsAllDrives' => true,
        ]);

        return $folder->getId();
    }

    public function fileExists(?string $fileId): bool
    {
        if (!$fileId) {
            return false;
        }

        try {
            $file = $this->drive->files->get($fileId, [
                'fields' => 'id,trashed',
                'supportsAllDrives' => true,
            ]);

            return !$file->getTrashed();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function previewUrl(?string $fileId): ?string
    {
        if (!$fileId) {
            return null;
        }

        try {
            $file = $this->drive->files->get($fileId, [
                'fields' => 'id,trashed,webViewLink',
                'supportsAllDrives' => true,
            ]);

            if ($file->getTrashed()) {
                return null;
            }

            return $file->getWebViewLink();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function download(string $fileId): string
    {
        if (!$fileId) {
            throw new RuntimeException('Drive file ID kosong.');
        }

        $httpClient = $this->client->authorize();

        $response = $httpClient->request(
            'GET',
            'https://www.googleapis.com/drive/v3/files/' . rawurlencode($fileId),
            [
                'query' => [
                    'alt' => 'media',
                    'supportsAllDrives' => 'true',
                ],
            ]
        );

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new RuntimeException('Gagal download file dari Google Drive. HTTP status: ' . $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    public function delete(string $fileId): void
    {
        if (!$fileId) {
            throw new RuntimeException('Drive file ID kosong.');
        }

        $this->drive->files->delete($fileId, [
            'supportsAllDrives' => true,
        ]);
    }

    private function sanitizeDriveName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[\\\\\/:*?"<>|]/', '-', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return mb_substr($name, 0, 150);
    }

    private function escapeDriveQueryValue(string $value): string
    {
        return str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
    }
}