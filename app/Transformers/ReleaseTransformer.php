<?php

namespace App\Transformers;

use App\Release;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

class ReleaseTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['builds', 'asset'];

    public function transform(Release $release)
    {
        return [
            'id' => $release->getRouteKey(),
            'version' => $release->version,
            'created_at' => $release->created_at->format('c'),
            'updated_at' => $release->updated_at->format('c'),
        ];
    }

    public function includeBuilds(Release $release)
    {
        return fractal()
            ->collection($release->builds)
            ->transformWith(new self())
            ->withResourceName('builds')
            ->getResource();
    }

    public function includeAsset(Release $release)
    {
        if (!$asset = $release->archive) {
            return $this->null();
        }

        return fractal()
            ->item($release->archive)
            ->transformWith(new AssetTransformer())
            ->withResourceName('assets')
            ->getResource();
    }
}
