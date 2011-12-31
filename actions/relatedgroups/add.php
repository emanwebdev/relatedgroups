<?php

	function relatedgroups_group_url_matches($url){
		$url = parse_url($url);
		$pattern1 = "/groups\/profile\/(?P<group_guid>\d+)/";
		$pattern2 = "/g\/(?P<group_alias>[^\/]+)/";
		
		$matches1 = array();
		$matches2 = array();
		
		preg_match($pattern1, $url['path'], $matches1);
		preg_match($pattern2, $url['path'], $matches2);
		
		if(!empty($matches1) || !empty($matches2)) {
			return array_merge($matches1, $matches2);
		} else {
			return false;
		}
	}

	function relatedgroups_get_group_from_url($group_url){
		$matches = relatedgroups_group_url_matches($group_url);
		$group_guid = $matches['group_guid'];
		$group_alias = $matches['group_alias'];
		
		$group = get_entity($group_guid);
		if(!$group && elgg_is_active_plugin('group_alias')) {
			$group = get_group_from_group_alias($group_alias);
		}
		
		if($group && $group->getURL() == $group_url){
			return $group;
		} else {
			return false;
		}
	}
	
	$group_guid = get_input('group');
	$othergroup_guid = get_input('othergroup');
	$othergroup_url = get_input('othergroup_url'); // maybe it isn't used
	$group = get_entity($group_guid);
	$othergroup = get_entity($othergroup_guid);

	if(!$othergroup && $othergroup = relatedgroups_get_group_from_url($othergroup_url)){
		$othergroup_guid = $othergroup->guid;
	}

	if ($group instanceof ElggGroup && $group->canEdit() && $othergroup instanceof ElggGroup) {
		if (!check_entity_relationship($group_guid, 'related', $othergroup_guid) && $group_guid != $othergroup_guid) {
			add_entity_relationship($group_guid, 'related', $othergroup_guid);
		}
	}
	else{
		register_error(elgg_echo('relatedgroups:add:error'));
	}
	forward(REFERER);
?>
