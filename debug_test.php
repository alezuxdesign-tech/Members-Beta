<?php
$dir = 'config';
$dirname = basename( $dir ); // 'config'
$class_name_suffix = str_replace( '-', '_', ucwords( $dirname, '-' ) ); 
echo "Original: '$dirname'\n";
echo "Suffix: '$class_name_suffix'\n";

$full_class_name = "\\Alezux_Members\\Modules\\{$class_name_suffix}\\{$class_name_suffix}";
echo "Full Class: '$full_class_name'\n";
