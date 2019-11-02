<?php


namespace App\Transformers;

use App\Models\Information;
use League\Fractal\TransformerAbstract;

class InformationTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(Information $information)
    {
        return [
            'id'           => $information->id,
            'user_id'      => $information->user_id,
            'contact'      => $information->contact,
            'contact_info' => $information->contact_info,
            'location'     => $information->location,
            'summary'      => $information->summary,
            'created_at'   => (string) $information->created_at,
            'updated_at'   => (string) $information->updated_at,
        ];
    }

    public function includeUser(Information $information)
    {
        return $this->item($information->user, new UserTransformer());
    }
}
