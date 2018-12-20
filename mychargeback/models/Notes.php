<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Notes extends Orm
{
    public $timestamps = false;
    protected $fillable = [
        'note',
        'date',
        'is_sticky',
    ];

    const NOTE_NULL_DATE = '2018-11-06';

    public static function updateSticky($dealId, $stickyValue)
    {
        self::selectRaw('*')
            ->join('note_relations', 'notes.id', '=', 'note_relations.note_id')
            ->where('note_relations.deal_id', $dealId)
            ->update(['is_sticky' => $stickyValue]);
    }

    public static function getCreatedNote($id)
    {
        $note =  self::with('docs', 'cards', 'deals')
            ->where('id', $id)
            ->first();
        PipedriveDeals::getNoteGroup($note);
        return $note;
    }

    public static function getCaseStickyNote($dealId)
    {
        return self::select('note')
            ->join('note_relations', 'note_relations.note_id', '=', 'notes.id')
            ->where('note_relations.deal_id', $dealId)
            ->orderBy('notes.is_sticky', 'DESC')
            ->first();
    }

    /* Getters */

    public function getDateAttribute($value)
    {
        $timezone = Auth::user()->timezone;
        if(!$value)
            $value = self::NOTE_NULL_DATE . ' 22:00:00';
        return $timezone
            ? (new \DateTime($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($timezone))->format('Y-m-d H:i:s')
            : $value;
    }

    /* Relations */

    public function docs()
    {
        return $this->hasMany(NoteDocs::className(), 'note_id');
    }

    public function cards()
    {
        return $this->hasMany(NoteRelations::className(), 'note_id')
            ->join('credit_cards', 'credit_cards.id', '=', 'note_relations.credit_card_id')
            ->whereNotNull("note_relations.credit_card_id");
    }

    public function deals()
    {
        return $this->hasMany(NoteRelations::className(), 'note_id')
            ->join('pipedrive_deals', 'pipedrive_deals.id', '=', 'note_relations.deal_id')
            ->whereNull("note_relations.credit_card_id");
    }
}
