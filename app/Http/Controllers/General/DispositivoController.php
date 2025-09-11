<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\PermisosController;
use App\Models\General\Usuarios\Usuario;
use App\Models\General\Usuarios_permiso;
use App\Models\General\Permiso;
use App\Models\General\Dispositivo;

use Illuminate\Http\Request;
use Inertia\Inertia;
use UAParser\Parser;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\type;

class DispositivoController extends Controller
{

    public function informacion()

    {
        $tablet_browser = 0;
        $mobile_browser = 0;

        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $tablet_browser++;
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ',
            'acs-',
            'alav',
            'alca',
            'amoi',
            'audi',
            'avan',
            'benq',
            'bird',
            'blac',
            'blaz',
            'brew',
            'cell',
            'cldc',
            'cmd-',
            'dang',
            'doco',
            'eric',
            'hipt',
            'inno',
            'ipaq',
            'java',
            'jigs',
            'kddi',
            'keji',
            'leno',
            'lg-c',
            'lg-d',
            'lg-g',
            'lge-',
            'maui',
            'maxo',
            'midp',
            'mits',
            'mmef',
            'mobi',
            'mot-',
            'moto',
            'mwbp',
            'nec-',
            'newt',
            'noki',
            'palm',
            'pana',
            'pant',
            'phil',
            'play',
            'port',
            'prox',
            'qwap',
            'sage',
            'sams',
            'sany',
            'sch-',
            'sec-',
            'send',
            'seri',
            'sgh-',
            'shar',
            'sie-',
            'siem',
            'smal',
            'smar',
            'sony',
            'sph-',
            'symb',
            't-mo',
            'teli',
            'tim-',
            'tosh',
            'tsm-',
            'upg1',
            'upsi',
            'vk-v',
            'voda',
            'wap-',
            'wapa',
            'wapi',
            'wapp',
            'wapr',
            'webc',
            'winw',
            'winw',
            'xda ',
            'xda-'
        );

        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) ? $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'] : (isset($_SERVER['HTTP_DEVICE_STOCK_UA']) ? $_SERVER['HTTP_DEVICE_STOCK_UA'] : ''));
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
                $tablet_browser++;
            }
        }
        if ($tablet_browser > 0) {
            // Si es dispositivo mobil has lo que necesites
            $agenteDeUsuario = $_SERVER["HTTP_USER_AGENT"];
            $parseador = Parser::create();
            $resultado = $parseador->parse($agenteDeUsuario);

            // $familiaNavegador = $resultado->ua->family; 
            // $navegador = $resultado->ua->toString();
            $dispositivo = $resultado->device->family;
            // $familiaSistema = $resultado->os->family;
            // $sistema = $resultado->os->toString();
            // $completo = $resultado->toString();
            $result = (object)[
                'tipo' => 'MÓVIL',
                'nombre' => mb_strtoupper($dispositivo),
                'autorizado' => true
            ];
            return $result;
        } else if ($mobile_browser > 0) {
            // Si es dispositivo mobil has lo que necesites
            $agenteDeUsuario = $_SERVER["HTTP_USER_AGENT"];
            $parseador = Parser::create();
            $resultado = $parseador->parse($agenteDeUsuario);

            // $familiaNavegador = $resultado->ua->family; 
            // $navegador = $resultado->ua->toString();
            $dispositivo = $resultado->device->family;
            // $familiaSistema = $resultado->os->family;
            // $sistema = $resultado->os->toString();
            // $completo = $resultado->toString();

            $result = (object)[
                'tipo' => 'MÓVIL',
                'nombre' => mb_strtoupper($dispositivo),
                'autorizado' => true
            ];

            return $result;
        } else {

            $dispositivo = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $result = (object)[
                'tipo' => 'COMPUTADORA',
                'nombre' => mb_strtoupper($dispositivo),
                'autorizado' => true
            ];

            return $result;
        }
    }
}
