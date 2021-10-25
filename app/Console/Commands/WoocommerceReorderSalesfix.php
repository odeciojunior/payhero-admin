<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;


class WoocommerceReorderSalesfix extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'WoocommerceReorderSalesfix';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    protected $str = 'erro -> JSON ERROR: Syntax error
    sucesso: 1267483 - https://descontofast.com
    sucesso: 1268236 - https://relogiospremium.com.br/
    sucesso: 1268240 - https://relogiospremium.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> Error: Desculpe, você não possui permissão para criar recursos. [woocommerce_rest_cannot_create]
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1270020 - https://bestbuyathome.com.br
    sucesso: 1270130 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1270808 - https://descontofast.com
    sucesso: 1270843 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1271631 - https://lojahousemais.com.br/
    sucesso: 1271797 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1272953 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1273397 - https://descontofast.com
    sucesso: 1273490 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1275596 - https://imperioroyal.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1275842 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1275966 - https://imperioroyal.com.br
    sucesso: 1276037 - https://bestbuyathome.com.br
    sucesso: 1276517 - https://descontofast.com
    sucesso: 1277071 - https://cabanahut.com.br/
    sucesso: 1277088 - https://cabanahut.com.br/
    sucesso: 1277093 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    sucesso: 1277151 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1277391 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1277476 - https://cabanahut.com.br/
    sucesso: 1277478 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1277480 - https://cabanahut.com.br/
    sucesso: 1277483 - https://cabanahut.com.br/
    sucesso: 1277485 - https://cabanahut.com.br/
    sucesso: 1277494 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1277517 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1277525 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1277537 - https://cabanahut.com.br/
    sucesso: 1277541 - https://cabanahut.com.br/
    sucesso: 1277542 - https://cabanahut.com.br/
    sucesso: 1277543 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1277546 - https://cabanahut.com.br/
    sucesso: 1277547 - https://cabanahut.com.br/
    sucesso: 1277553 - https://cabanahut.com.br/
    sucesso: 1277562 - https://cabanahut.com.br/
    sucesso: 1277570 - https://cabanahut.com.br/
    sucesso: 1277572 - https://cabanahut.com.br/
    sucesso: 1277573 - https://cabanahut.com.br/
    sucesso: 1277583 - https://cabanahut.com.br/
    sucesso: 1277584 - https://cabanahut.com.br/
    sucesso: 1277585 - https://cabanahut.com.br/
    sucesso: 1277594 - https://cabanahut.com.br/
    sucesso: 1277951 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1278181 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1278727 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1279652 - https://ryoacessorios.com.br/
    sucesso: 1279913 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1280173 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1281157 - https://bestbuyathome.com.br
    sucesso: 1281279 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1281485 - https://ryoacessorios.com.br/
    sucesso: 1281579 - https://mundodofut.com/
    sucesso: 1281773 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1282126 - https://mundodofut.com/
    sucesso: 1282215 - https://descontofast.com
    sucesso: 1282303 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1282517 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1282824 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1283056 - https://suportenotebookib.com.br/
    sucesso: 1283171 - https://ryoacessorios.com.br/
    sucesso: 1283187 - https://suportenotebookib.com.br/
    sucesso: 1283209 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1283925 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1284252 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1285418 - https://suportenotebookib.com.br/
    sucesso: 1285477 - https://gatosaltitante.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1285967 - https://descontofast.com
    sucesso: 1286144 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1286817 - https://imperioroyal.com.br
    sucesso: 1287590 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1288127 - https://descontofast.com
    sucesso: 1288153 - https://descontofast.com
    sucesso: 1288379 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1289168 - https://ryoacessorios.com.br/
    sucesso: 1289938 - https://suportenotebookib.com.br/
    sucesso: 1289946 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1290119 - https://bestbuyathome.com.br
    sucesso: 1290196 - https://autenticoshop.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1290334 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1290466 - https://lojahousemais.com.br/
    sucesso: 1290527 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1291122 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1292123 - https://suportenotebookib.com.br/
    sucesso: 1292325 - https://suportenotebookib.com.br/
    sucesso: 1292479 - https://ryoacessorios.com.br/
    sucesso: 1292609 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1292926 - https://gatosaltitante.com.br/
    sucesso: 1293178 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1293586 - https://lojahousemais.com.br/
    sucesso: 1293588 - https://lojahousemais.com.br/
    sucesso: 1293838 - https://ryoacessorios.com.br/
    sucesso: 1293907 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    sucesso: 1294019 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1294290 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1294485 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1294572 - https://descontofast.com
    erro -> A non well formed numeric value encountered
    sucesso: 1294678 - https://perfecteletronicos.com.br/
    sucesso: 1294818 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1294890 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1295868 - https://bestbuyathome.com.br
    sucesso: 1295997 - https://descontofast.com
    sucesso: 1296011 - https://cabanahut.com.br/
    sucesso: 1296054 - https://cabanahut.com.br/
    sucesso: 1296181 - https://autenticoshop.com
    sucesso: 1296221 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    sucesso: 1296489 - https://bestbuyathome.com.br
    sucesso: 1296634 - https://cabanahut.com.br/
    sucesso: 1296815 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1296857 - https://descontofast.com
    sucesso: 1297031 - https://descontofast.com
    sucesso: 1297129 - https://bestbuyathome.com.br
    sucesso: 1297267 - https://descontofast.com
    sucesso: 1297283 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1297351 - https://ryoacessorios.com.br/
    sucesso: 1297361 - https://ryoacessorios.com.br/
    sucesso: 1297421 - https://descontofast.com
    sucesso: 1297522 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1297580 - https://ryoacessorios.com.br/
    sucesso: 1297837 - https://cabanahut.com.br/
    sucesso: 1298190 - https://cabanahut.com.br/
    sucesso: 1298211 - https://ryoacessorios.com.br/
    sucesso: 1298393 - https://bestbuyathome.com.br
    sucesso: 1298498 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> A non well formed numeric value encountered
    erro -> A non well formed numeric value encountered
    sucesso: 1299116 - https://luxetech.com.br/
    sucesso: 1299122 - https://cabanahut.com.br/
    sucesso: 1299203 - https://cabanahut.com.br/
    sucesso: 1299240 - https://cabanahut.com.br/
    sucesso: 1299268 - https://cabanahut.com.br/
    sucesso: 1299427 - https://suportenotebookib.com.br/
    sucesso: 1299645 - https://cabanahut.com.br/
    sucesso: 1299742 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1299884 - https://cabanahut.com.br/
    sucesso: 1299893 - https://ryoacessorios.com.br/
    sucesso: 1300003 - https://ryoacessorios.com.br/
    sucesso: 1300104 - https://cabanahut.com.br/
    sucesso: 1300267 - https://ryoacessorios.com.br/
    sucesso: 1300371 - https://cabanahut.com.br/
    sucesso: 1300392 - https://bestbuyathome.com.br
    sucesso: 1300489 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1301052 - https://cabanahut.com.br/
    sucesso: 1301053 - https://cabanahut.com.br/
    sucesso: 1301063 - https://cabanahut.com.br/
    sucesso: 1301177 - https://cabanahut.com.br/
    sucesso: 1301272 - https://cabanahut.com.br/
    erro -> A non well formed numeric value encountered
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1301545 - https://cabanahut.com.br/
    sucesso: 1301830 - https://imperioroyal.com.br
    erro -> JSON ERROR: Syntax error
    sucesso: 1301900 - https://bestbuyathome.com.br
    sucesso: 1301910 - https://bestbuyathome.com.br
    sucesso: 1301964 - https://luxetech.com.br/
    sucesso: 1302011 - https://cabanahut.com.br/
    sucesso: 1302125 - https://luxetech.com.br/
    sucesso: 1302135 - https://cabanahut.com.br/
    sucesso: 1302145 - https://luxetech.com.br/
    sucesso: 1302159 - https://cabanahut.com.br/
    sucesso: 1302199 - https://ryoacessorios.com.br/
    sucesso: 1302208 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1302365 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1302527 - https://ryoacessorios.com.br/
    sucesso: 1302632 - https://ryoacessorios.com.br/
    sucesso: 1302696 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1303008 - https://autenticoshop.com
    sucesso: 1303050 - https://ryoacessorios.com.br/
    sucesso: 1303093 - https://cabanahut.com.br/
    sucesso: 1303095 - https://bestbuyathome.com.br
    sucesso: 1303206 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1303334 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1303384 - https://cabanahut.com.br/
    sucesso: 1303399 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1303450 - https://cabanahut.com.br/
    sucesso: 1303472 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1303565 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1303700 - https://suportenotebookib.com.br/
    sucesso: 1303810 - https://cabanahut.com.br/
    sucesso: 1304126 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1304195 - https://imperioroyal.com.br
    sucesso: 1304454 - https://autenticoshop.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1304878 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1305365 - https://ryoacessorios.com.br/
    sucesso: 1305399 - https://cabanahut.com.br/
    sucesso: 1305406 - https://cabanahut.com.br/
    sucesso: 1305414 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1305608 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1305679 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1305793 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1305864 - https://cabanahut.com.br/
    sucesso: 1306054 - https://ryoacessorios.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1306143 - https://lojahousemais.com.br/
    sucesso: 1306197 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1306251 - https://bestbuyathome.com.br
    sucesso: 1306326 - https://cabanahut.com.br/
    sucesso: 1306343 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1306680 - https://cabanahut.com.br/
    sucesso: 1306688 - https://cabanahut.com.br/
    sucesso: 1306692 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1306829 - https://lojahousemais.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> A non well formed numeric value encountered
    sucesso: 1307115 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    sucesso: 1307255 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1307446 - https://ryoacessorios.com.br/
    sucesso: 1307474 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1307754 - https://imperioroyal.com.br
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> cURL Error: Operation timed out after 15000 milliseconds with 0 bytes received
    sucesso: 1308103 - https://suportenotebookib.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1308377 - https://lojahousemais.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1308512 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1308629 - https://luxetech.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1308793 - https://suportenotebookib.com.br/
    sucesso: 1308795 - https://luxetech.com.br/
    sucesso: 1308804 - https://luxetech.com.br/
    sucesso: 1308867 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1308931 - https://lojahousemais.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1308948 - https://cabanahut.com.br/
    sucesso: 1308966 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1309002 - https://luxetech.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1309074 - https://luxetech.com.br/
    erro -> A non well formed numeric value encountered
    sucesso: 1309100 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1309328 - https://ryoacessorios.com.br/
    sucesso: 1309383 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1309457 - https://cabanahut.com.br/
    sucesso: 1309609 - https://ryoacessorios.com.br/
    sucesso: 1311443 - https://ryoacessorios.com.br/
    sucesso: 1311610 - https://suportenotebookib.com.br/
    sucesso: 1312484 - https://suportenotebookib.com.br/
    sucesso: 1312755 - https://cabanahut.com.br/
    sucesso: 1312900 - https://suportenotebookib.com.br/
    sucesso: 1313002 - https://suportenotebookib.com.br/
    erro -> A non well formed numeric value encountered
    sucesso: 1313343 - https://luxetech.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1313452 - https://cabanahut.com.br/
    erro -> A non well formed numeric value encountered
    erro -> A non well formed numeric value encountered
    sucesso: 1313717 - https://ryoacessorios.com.br/
    sucesso: 1313916 - https://suportenotebookib.com.br/
    sucesso: 1314153 - https://cabanahut.com.br/
    sucesso: 1314182 - https://cabanahut.com.br/
    sucesso: 1314189 - https://cabanahut.com.br/
    sucesso: 1314193 - https://cabanahut.com.br/
    sucesso: 1314208 - https://cabanahut.com.br/
    sucesso: 1314213 - https://cabanahut.com.br/
    sucesso: 1314217 - https://cabanahut.com.br/
    sucesso: 1314223 - https://cabanahut.com.br/
    sucesso: 1314241 - https://cabanahut.com.br/
    sucesso: 1314254 - https://cabanahut.com.br/
    sucesso: 1314279 - https://cabanahut.com.br/
    sucesso: 1314332 - https://cabanahut.com.br/
    sucesso: 1314342 - https://cabanahut.com.br/
    sucesso: 1314400 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1314420 - https://imperioroyal.com.br
    erro -> A non well formed numeric value encountered
    sucesso: 1314451 - https://cabanahut.com.br/
    sucesso: 1314468 - https://suportenotebookib.com.br/
    sucesso: 1314476 - https://cabanahut.com.br/
    sucesso: 1314483 - https://cabanahut.com.br/
    sucesso: 1314512 - https://ryoacessorios.com.br/
    sucesso: 1314571 - https://luxetech.com.br/
    sucesso: 1314594 - https://cabanahut.com.br/
    sucesso: 1314635 - https://cabanahut.com.br/
    sucesso: 1314996 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    erro -> JSON ERROR: Syntax error
    sucesso: 1315206 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1315278 - https://suportenotebookib.com.br/
    erro -> A non well formed numeric value encountered
    sucesso: 1315354 - https://cabanahut.com.br/
    sucesso: 1315371 - https://cabanahut.com.br/
    erro -> A non well formed numeric value encountered
    erro -> JSON ERROR: Syntax error
    erro -> cURL Error: Operation timed out after 15000 milliseconds with 0 bytes received
    erro -> cURL Error: Operation timed out after 15000 milliseconds with 0 bytes received
    sucesso: 1315606 - https://cabanahut.com.br/
    erro -> A non well formed numeric value encountered
    sucesso: 1315626 - https://cabanahut.com.br/
    erro -> A non well formed numeric value encountered
    erro -> JSON ERROR: Syntax error
    sucesso: 1315678 - https://descontofast.com
    sucesso: 1315681 - https://cabanahut.com.br/
    sucesso: 1315683 - https://descontofast.com
    erro -> A non well formed numeric value encountered
    sucesso: 1315705 - https://ryoacessorios.com.br/
    sucesso: 1315717 - https://cabanahut.com.br/
    erro -> JSON ERROR: Syntax error
    sucesso: 1315803 - https://cabanahut.com.br/
    erro -> A non well formed numeric value encountered
    erro -> JSON ERROR: Syntax error
    erro -> A non well formed numeric value encountered
    sucesso: 1315889 - https://suportenotebookib.com.br/
    sucesso: 1315917 - https://descontofast.com
    erro -> JSON ERROR: Syntax error
    sucesso: 1315937 - https://bestbuyathome.com.br
    erro -> JSON ERROR: Syntax error
    ';
    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $this->str) as $line){
            // do stuff with $line
            if(stristr($line, 'sucesso')){
                $line = explode(' ', $line);

                $saleModel     = new Sale();
                $sales         = $saleModel->where('id', $line[5])->first();

                
                
                if(!empty($sales)){
                    

                    if($sales->status != 1 && !empty($sales->woocommerce_order)){
                        
                        $integration = WooCommerceIntegration::where('project_id', $sales->project_id)->first();
                        
                        $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
                        
                        $service->cancelOrder($sales);
                        
                        $this->line('status: '.$sales->status.' sale: '.$sales->id.' order: '.$sales->woocommerce_order.' url: '.$integration->url_store.' :: fixed.');
                    }

                }else{
                    $this->line('not found: '.$line[5]);

                }


                // $this->line($line[5]);
                
            }
            
        } 
        
        $this->line('done!');
    }

}
