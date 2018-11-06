<?php

class TagType extends Eloquent {
	
	protected $table = 'com_tag_types';
	protected $primaryKey = 'tag_type_id';

        public function tags() {
            return $this->hasMany('Tag', 'tag_type_id', 'tag_type_id');
        }
}