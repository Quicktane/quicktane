<?php

declare(strict_types=1);

namespace Quicktane\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Media\Contracts\MediaFacade;
use Quicktane\Media\Http\Requests\UpdateMediaFileRequest;
use Quicktane\Media\Http\Requests\UploadMediaFileRequest;
use Quicktane\Media\Http\Resources\MediaFileResource;
use Quicktane\Media\Models\MediaFile;
use Quicktane\Media\Repositories\MediaFileRepository;

class MediaFileController extends Controller
{
    public function __construct(
        private readonly MediaFileRepository $mediaFileRepository,
        private readonly MediaFacade $mediaFacade,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $mediaFiles = $this->mediaFileRepository->paginate($perPage);

        return MediaFileResource::collection($mediaFiles->load('variants'));
    }

    public function store(UploadMediaFileRequest $request): JsonResponse
    {
        $mediaFile = $this->mediaFacade->upload($request->file('file'));

        return (new MediaFileResource($mediaFile))
            ->response()
            ->setStatusCode(201);
    }

    public function show(MediaFile $file): MediaFileResource
    {
        return new MediaFileResource($file->load('variants'));
    }

    public function update(UpdateMediaFileRequest $request, MediaFile $file): MediaFileResource
    {
        $mediaFile = $this->mediaFacade->updateFileMeta($file, $request->validated());

        return new MediaFileResource($mediaFile->load('variants'));
    }

    public function destroy(MediaFile $file): JsonResponse
    {
        $this->mediaFacade->deleteFile($file);

        return response()->json(null, 204);
    }
}
