#!/bin/sh

# 检查参数
function Usage()
{
	echo
	echo  "Program that generate server framework based on Yii."
	echo  "Usage:"
	echo  "\t$0 system_name module_name [dest_dir]"
	echo
	echo  "params:"
	echo  "\tsystem_name system name"
	echo  "\tsmodule_name module name"
	echo  "\tdest_dir destination directory(dir that not exits)."
	echo  "\t\tdefault: $PWD"
	echo
	exit
}
argc=${#@}
if [ $argc -lt 2 ] 
then
	Usage
fi

SRC_DIR=/tmp/genServer/
SRC_PKG=""
mkdir -p $SRC_DIR

system_name=$1
module_name=$2
dest_dir=$3
if [ "M$dest_dir" = "M" ]
then
	dest_dir=$PWD
fi
system_dir=$dest_dir
module_dir=$dest_dir/protected/modules/$module_name

# 检查目录
if [ ! -d $system_dir ]
then
	mkdir -p `dirname $system_dir`
	cp -r . $system_dir
elif [ "$system_dir" != "$PWD" ]
then
	echo "system_dir: ${system_dir} already exists"
	Usage
fi

if [ ! -d $module_dir ]
then
	echo "make module[$module_name]"
	cp -r ./protected/modules/demo $module_dir
else
	echo "module[$module_name] already exists"
	Usage
fi

# 修改配置
sed -i "" "/define(\s*.SYSTEM_NAME./s/server/$system_name/" $system_dir/protected/config/base.php
UpModule_name=`php -r "echo ucwords('$module_name');"`
if [ ! -x $module_dir/${UpModule_name}Module.php ]
then
	mv $module_dir/DemoModule.php $module_dir/${UpModule_name}Module.php
fi
count=`grep -c "'$module_name'\s*=>\s*array(\s*)\s*," $system_dir/protected/config/base.php`
if [ $count -lt 1 ]
then
	#echo "here"
	sed -i "" "/\'modules\'\s*=>\s*array\s*(/a\ 
		    '$module_name' => array(),
" $system_dir/protected/config/base.php	
fi
sed -i "" "/application\.modules\.base/s/\.base/\.$module_name/" $module_dir/config/base.php

#增加testcase入口文件
cat <<EOF >$system_dir/protected/tests/test_${module_name}.sh
#!/bin/bash

APPLICATION_ENV='development'
MODULE_NAME='$module_name'

export APPLICATION_ENV MODULE_NAME
phpunit --colors -v --debug --coverage-html ~/data/coverage/ --testsuite "\$MODULE_NAME" "\$1"
EOF

##修改phpunit.xml文件
sed -i "" "/<testsuites>/a\\
<testsuite name=\"$module_name\">\\
    <directory suffix=\"Test.php\" phpVersion=\"5.3.0\" phpVersionOperator=\">=\">../modules/$module_name/tests/</directory>\\
</testsuite>\\
" $system_dir/protected/tests/phpunit.xml

echo "OK"
#echo $flag
#echo $system_name
#echo $module_name
#echo $system_dir
#echo $module_dir
#echo $PWD
