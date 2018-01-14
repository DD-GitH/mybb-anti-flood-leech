<?php
function egaux($fids)
{
    if (1 === count(array_unique($fids)))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function can_reply()
{
    global $db, $mybb, $thread;
    // Avoir les messages
    $fids = explode(",",$mybb->settings['antiflood_fids']);
    if (in_array($thread["fid"],$fids) AND $mybb->user["postnum"] != 0)
    {
        $uid = $mybb->user["uid"];
        $max = intval($mybb->settings["antiflood_max"]);
        $query ="SELECT * FROM ".TABLE_PREFIX."posts WHERE uid = $uid ORDER BY `dateline` DESC LIMIT $max;";
        $req = $db->query($query);
        // Ajout des FIDs des messages dans un tableau
        while ($post = $db->fetch_array($req))
        {
            $fid = intval($post["fid"]);
            array_push($fids, $fid);
        }
        // Verification des FIDs si il est le même
        if (egaux($fids))
        {
            $deny = 1;
        }
        else
        {
            $deny = 0;
        }
        // Avoir les messages si les FIDs ne sont pas les mêmes
        if ($deny == 0)
        {
            $needed = intval($mybb->settings["antiflood_needed"]);
            $max = $needed + $max - 1;
            unset($fids);
            $fids = array();
            $query ="SELECT * FROM ".TABLE_PREFIX."posts WHERE uid = $uid ORDER BY `dateline` DESC LIMIT $needed,$max;";
            $req = $db->query($query);
            // Ajout des FIDs des messages dans un tableau
            while ($post = $db->fetch_array($req))
            {
                $fid = intval($post["fid"]);
                array_push($fids, $fid);
            }
            // Verification des FIDs si il est le même
            if (egaux($fids))
            {
                $deny = 1;
            }
            else
            {
                $deny = 0;
            }
        }
        if ($deny == 0)
        {
            return true;
        }
        elseif ($deny == 1)
        {
            return false;
        }
    }
    else
    {
        return true;   
    }
}
function AFquickreply()
{
    global $mybb, $quickreply;
    if(!can_reply())
    {
        $quickreply= '';
    }
}
function AFreply()
{
    global $mybb;
    if(!can_reply())
    {
        header("Location: antiflood.php");
    }
}
function create_AFsettings()
{
    global $db, $lang;
    $lang->load("antiflood");
	$setting_group = array(
		'name'			=> 'antiflood',
		'title'			=> $lang->antiflood_group_title,
		'description'	=> $lang->antiflood_group_desc,
		'disporder'		=> '1'
	);
    $db->insert_query('settinggroups', $setting_group);
	$gid = $db->insert_id();
    
    $antiflood_setting = array(
        'name'			=> 'antiflood_max',
		'title'			=> $lang->antiflood_max_title,
		'description'	=> $lang->antiflood_max_desc,
		'optionscode'	=> 'text', // This will be a textbox
		'value'			=> '', // This will be the the contents of the box
		'disporder'		=> '2', // The order that the settings are displayed in the group
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $antiflood_setting);
    
    $antiflood_setting2 = array(
        'name'			=> 'antiflood_needed',
		'title'			=> $lang->antiflood_needed_title,
		'description'	=> $lang->antiflood_needed_desc,
		'optionscode'	=> 'text', // This will be a textarea
		'value'			=> '', // This will be the the contents of the box
		'disporder'		=> '3', // The order that the settings are displayed in the group
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $antiflood_setting2);
    
    $antiflood_setting3 = array(
        'name'			=> 'antiflood_fids',
		'title'			=> $lang->antiflood_fids_title,
		'description'	=> $lang->antiflood_fids_desc,
		'optionscode'	=> 'textarea', // This will be a textarea
		'value'			=> '', // This will be the the contents of the box
		'disporder'		=> '1', // The order that the settings are displayed in the group
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $antiflood_setting3);
    
    $antiflood_setting4 = array(
        'name'			=> 'antiflood_message',
		'title'			=> $lang->antiflood_message_title,
		'description'	=> $lang->antiflood_message_desc,
		'optionscode'	=> 'textarea', // This will be a textarea
		'value'			=> 'You need to post in another section before replying to this thread', // This will be the the contents of the box
		'disporder'		=> '4', // The order that the settings are displayed in the group
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $antiflood_setting4);
    rebuild_settings();
}

function delete_AFsettings()
{
    global $db;
    $db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('antiflood_max','antiflood_needed','antiflood_fids','antiflood_message')");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name = 'antiflood'");
	rebuild_settings();
}

function create_AFtemplate()
{
    global $lang;
    $lang->load("antiflood");
    global $db, $mybb;
    $template = '<html>
<head>
<title>{$mybb->settings[bbname]}</title>
{$headerinclude}
</head>
<body>
{$header}
<br />
<!-- Content: Start -->
{$lang->antiflood}</br>
<!-- Content: End -->
{$footer}
</body>
</html>';

$insert_array = array(
    'title' => 'antiflood',
    'template' => $db->escape_string($template),
    'sid' => '-1',
    'version' => '',
    'dateline' => time()
);

$db->insert_query('templates', $insert_array);
}
function delete_AFtemplate()
{
    global $db;
    $db->delete_query("templates", "title = 'antiflood'");
}