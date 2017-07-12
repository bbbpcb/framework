<?php
$id=intval(I("id"));
M("chanyeyuan",null,"`id`={$id}");
script("BirdPost(this,'.SavedListPost','clist','#SavedTableFrame');");