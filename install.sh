#!/bin/bash
echo "开始安装composer包"
composer install
echo "开始更新数据库"
php bin/console doctrine:migrations:migrate

echo "开始生成定时任务"
if [ ! -e /etc/crontab ] ; then
    echo "SHELL=/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin
MAILTO=root

# For details see man 4 crontabs

# Example of job definition:
# .---------------- minute (0 - 59)
# |  .------------- hour (0 - 23)
# |  |  .---------- day of month (1 - 31)
# |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ...
# |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
# |  |  |  |  |
# *  *  *  *  * user-name  command to be executed" | sudo tee /etc/crontab
fi

function existOrNewCron()
{
    name=$1
    grepName=$2
    existOrNewCronValExist=`grep -c "$grepName" /etc/crontab`
    if [ $existOrNewCronValExist -eq 0 ] ; then
        echo "$name" | sudo tee -a /etc/crontab 
    fi
}

existOrNewCron "00 *    * * *   root    php "`pwd`"/bin/console app:list-pushes" "00 \*    \* \* \*   root    php "`pwd`"/bin/console app:list-pushes"
existOrNewCron "01 *    * * *   root    php "`pwd`"/bin/console app:send-score" "01 \*    \* \* \*   root    php "`pwd`"/bin/console app:send-score"

crontab /etc/crontab

echo "安装完成"