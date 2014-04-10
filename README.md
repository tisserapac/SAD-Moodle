SAD-Moodle
==========


How to Install

make sure you have apache, mysql and php running. For windows you can use Xampp or Wampp, for Ubuntu users just install apache-php5-mysql 

Download all files and import moodle.sql, modify config.php accordingly.



Configuring your Ubuntu
=========================

<code>sudo apt-get install php5-mysql apache2 libapache2-mod-php5</code>






Configuring Apache
====================

it's better to point your server to which your cloned project is, just edit the file /etc/apache2/sites-available/000-default.conf

modify DocumentRoot  to where your cloned project is

<pre><code>DocumentRoot /home/rey/Application/SAD-Moodle</code></pre>
Add the following just below the DocmentRoot

<pre><code>&lt;Directory /home/rey/Application/SAD-Moodle&gt;
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
&lt;/Directory&gt;</code></pre>

  
  
