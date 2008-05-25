<?php

function _mythumb_lightbox_callback($m) {
    global $mythumb;
    $src = $m[3];
    $name = mythumb_build($src,"800x400");
    if ($name) { $src = "{$mythumb["dataurl"]}/$name"; }
    $v = "{$m[1]}{$src}{$m[6]}";
    return substr($v,0,-1)." rel=\"lightbox[mythumb]\">";
}

function mythumb_lightbox($content) {
    $ne = "([^>]*?)";
    $qs = "['\"]";
    $nq = "([^'\"]*?)";
    $patt = "/(<a{$ne}href={$qs})({$nq}.(bmp|jpg|jpeg|gif|png))({$qs}{$ne}>)/i";
    $content = preg_replace_callback($patt,"_mythumb_lightbox_callback",$content);

    return $content;
}
?>