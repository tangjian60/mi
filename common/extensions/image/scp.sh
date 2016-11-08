#!/bin/sh

#write by lijianwei

#veimg ip
img_server_ip=$1;

#veimg username
img_server_user=$2;

#local file path
local_file_path=$3;

#remote file path
remote_file_path=$4;

#override value type must be int  >0 override  0 don't override
is_override=$5;

#check param num
if [ $# -lt 4 ]; then
	echo "param num must greater than 4";
	exit 1;
fi

#if $5 don't set value
if [ ! -n "$is_override" ]; then
	is_override=1;
fi

ssh $img_server_user@$img_server_ip "if [ -d '$remote_file_path' ]; then exit 0; else exit 1; fi";
if [ $? -eq 0 ]; then
	echo $remote_file_path ":Don't allow is a directory";
	exit 1;
fi

ssh $img_server_user@$img_server_ip "if [ -f '$remote_file_path' ]; then exit 0; else exit 1; fi";
if [ $? -eq 0 ] && [ "$is_override" -eq 0 ]; then
	exit 0;
fi

remote_dir=$(dirname $remote_file_path);

ssh $img_server_user@$img_server_ip "mkdir -p $remote_dir; if [ $? -eq 0 ]; then exit 0; else exit 1; fi";
if [ $? -eq 1 ]; then
	echo "mkdir $remote_dir occur error";
	exit 1;
fi


#default scp override while appear file exist
scp $local_file_path $img_server_user@$img_server_ip:$remote_file_path >/dev/null 2>&1;
if [ $? -eq  0 ]; then
	exit 0;
fi
echo "copy $local_file_path to $remote_file_path occur error!";
exit 1;