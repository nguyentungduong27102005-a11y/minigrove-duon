<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ChatAttachmentService
{
    /**
     * @param array<int, UploadedFile> $images
     */
    public function attachImages(
        Message $message,
        array $images
    ): void {
        $storedPaths = [];

        try {
            foreach ($images as $image) {
                $path = $image->store(
                    'chat/' . $message->conversation_id,
                    'public'
                );

                $storedPaths[] = $path;

                $message->attachments()->create([
                    'original_name' => $image->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $image->getMimeType()
                        ?: $image->getClientMimeType(),
                    'file_size' => $image->getSize(),
                    'attachment_type' => 'IMAGE',
                ]);
            }
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('public')->delete($storedPaths);
            }

            throw $exception;
        }
    }
}