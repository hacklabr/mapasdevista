#wget -c  http://wordpress.org/latest.zip
#unzip latest.zip
#mv wordpress/* .
#rm latest.zip
#rmdir wordpress
#cd wp-content
#ln -sf ../../dev_uploads/uploads/ .
#ln -sf ../../dev_uploads/blogs.dir/ .
#cd ..
#sed -i 's/<<SLUG>>/exemplo/g' src/_htaccess

# Adicionar linha:
# define('DOMAIN_CURRENT_SITE', 'caiman.com.br' );

#run_local('sed "s|define(\'DOMAIN_CURRENT_SITE\', \'.*\'|define(\'DOMAIN_CURRENT_SITE\', \'%s\'|g" -i %s/src/wp-config.php' % (self.slice_domain, tag))  vira    localhost/slug
#run_local('sed "s|define(\'DB_USER\', \'.*\');|define(\'DB_USER\', \'hacklab\');|g" -i %s/src/wp-config.php' % tag)
#run_local('sed "s|define(\'DB_PASSWORD\', \'.*\');|define(\'DB_PASSWORD\', \'%s\');|g" -i %s/src/wp-config.php ' % (safe_password(self.mysql_password), tag))
#run_local("sed \"s|define('DB_NAME', .*);|define('DB_NAME', '%s');|g\" -i %s/src/wp-config.php" % (self.name, tag))


