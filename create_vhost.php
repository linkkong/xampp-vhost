<?php
    function echo1($str){
        return iconv( 'UTF-8', 'GB2312',$str);
    }
    $dir = 'D:\xampp\apache';
    $www_dir = str_replace('apache','',$dir).'htdocs\\';
    $vhost_include = "\r\n".'Include '.$dir.'\conf\extra\vhost\\';
    $vhost_dir = $dir.'\conf\extra\vhost';
    $default_vhost = $dir.'\conf\extra\httpd-vhosts.conf';
    $windows_hosts = "C:\Windows\System32\drivers\\etc\hosts";
    if (!file_exists($default_vhost)){
        echo(echo1("没有httpd-vhosts.conf文件！"));
        die();
    }

    #判断是否存在引入语句
    #如果不存在就引入，并创建vhost文件夹
    if(!stristr(file_get_contents($default_vhost),'Include')){
        if(file_put_contents($default_vhost, $vhost_include, FILE_APPEND)){
            
            echo echo1("写入成功");
        }else{
            echo echo1("写入失败");
        }
    }
    #如果不存在vhost文件夹则新建一个
    if(!is_dir($vhost_dir)){
        mkdir($vhost_dir);
    }

    #获取域名
    fwrite(STDOUT,echo1("请输入域名\r\n"));
    $www = trim(fgets(STDIN));
    echo echo1('您输入的信息是：').$www."\r\n";

    fwrite(STDOUT,echo1("请输入目录\r\n"));
    $mulu = trim(fgets(STDIN));
    echo echo1('您输入的信息是：').$mulu."\r\n";

    fwrite(STDOUT,echo1("是否开始生成日志目录和Vhost文件（y/n，默认y）\r\n"));
    $boolen = trim(fgets(STDIN));
    if($boolen !='n' ){
        #创建域名.conf文件
        $documentRoot = str_replace('\\','/',$www_dir).$mulu;
$conf = <<<EOD
<VirtualHost *:80>
    ServerAdmin webmaster@dummy-host2.example.com
    DocumentRoot "$documentRoot"
    ServerName $www
    ErrorLog "|bin/rotatelogs.exe -l logs/$www/error-%Y-%m-%d.log 86400"
    CustomLog "|bin/rotatelogs.exe -l logs/$www/access-%Y-%m-%d.log 86400 "combined
    <FilesMatch \.(htaccess|htpasswd|svn|git)>
        Require all denied
    </FilesMatch>
    <Directory "$documentRoot">
        Options -Indexes
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>

EOD;
        file_put_contents($vhost_dir.'\\'.$www.'.conf', $conf, FILE_APPEND);
        #创建log文件夹
        $log_dir = $dir.'\logs\\'.$www;
        mkdir($log_dir);
        #重启apache服务器
        exec('net stop Apache2.4',$stop);
        foreach($stop as $sto){
            echo $sto."\r\n";
        }
        exec('net start Apache2.4',$start);
        foreach($start as $sta){
            echo $sta."\r\n";
        }
        #修改本机host文件
        $local_host = "\r\n".'127.0.0.1        '.$www;
        // echo file_get_contents($windows_hosts);
        file_put_contents($windows_hosts, $local_host, FILE_APPEND);
    }else{
        die(echo1("您取消了操作！拜拜！"));
    }
