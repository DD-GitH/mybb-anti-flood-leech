<?php
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook("newreply_start", "AFreply");
$plugins->add_hook("showthread_end", "AFquickreply");


function antiflood_info()
{
    global $lang;
    $lang->load("antiflood");
    return array(
        "name"          => $lang->title_plugin,
        "description"   => $lang->desc_plugin,
        "website"       => "https://developement.design/",
        "author"        => "AmazOuz",
        "authorsite"    => "https://developement.design/",
        "version"       => "1.0",
        "guid"          => "",
        "codename"      => "antiflood",
        "compatibility" => "*"
    );
}


function antiflood_activate()
{
    create_AFtemplate();
    create_AFsettings();
}

function antiflood_deactivate()
{
    delete_AFtemplate();
    delete_AFsettings();
}

include 'antiflood/functions.php';