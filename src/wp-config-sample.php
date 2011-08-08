<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'mapasdevista');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');
define('DOMAIN_CURRENT_SITE', 'localhost/mapasdevista');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'tly7,JwY5(-<nKxl{V-TZ9#6&cYnCmxtO/0|Fo-HH$}]eD{ez;oMuG_KkJ1wI>-Z');
define('SECURE_AUTH_KEY',  '%/Dn*0P,C5dOB6hkZ27b1R:-^X*(dy?o$FI]bwP/fuUxl@r/AvisuoSz:-0K?RfR');
define('LOGGED_IN_KEY',    'paR<d}@Kn4c+*>oe}W O/;5 TLjpa2|!Lk(<|=[AWxJ9%b~y> YQ0}k,9{6<~`B)');
define('NONCE_KEY',        '`y{V/e_rdyiq?P7VL;X.sqE|{Bl!6`W=PVRxQK1+mNQGx:Mr9;2,+vD|S*rHQ/i|');
define('AUTH_SALT',        '|)>t7>0D6.-axO7|-||e{^B&^;vb=f([c{-Lhs}D*!VZ pL;lHJ6-R}0;ftr|6 9');
define('SECURE_AUTH_SALT', '=Whc~p2+G;Fd>)SR`q&x+mIgW/d>S58<x d+(~DCY6PrA9/K]Xxu2!~#H5~Y6bY-');
define('LOGGED_IN_SALT',   'nkJ@)^?~PL1dzd>(;Pe?0=!+6!?9#V=y7+?`?]+-58`R]<+W}:rRNT}M[P=83vH-');
define('NONCE_SALT',       '`Fyt~QJ[K9k;Z[EA|s/*/M^Skf2~-?58mgr Lp>X].0w|EX/{>}u~z3 g,smsK-,');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
