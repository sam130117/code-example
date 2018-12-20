<?php

namespace App\Models;


class NoteRelations extends Orm
{
    public $timestamps = false;
    protected $fillable = [
        'note_id',
        'credit_card_id',
        'deal_id',
    ];

    public static function getNoteRelations($relationIds, $dealId, $noteId)
    {
        $noteRelations = [];
        foreach ($relationIds as $relationId) {
            if ($relationId == 'general')
                $noteRelations[] = ['note_id' => $noteId, 'credit_card_id' => null, 'deal_id' => $dealId];
            else $noteRelations[] = ['note_id' => $noteId, 'credit_card_id' => $relationId, 'deal_id' => $dealId];
        }
        return $noteRelations;
    }
}
