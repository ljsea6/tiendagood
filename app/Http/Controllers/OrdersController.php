<?php

namespace App\Http\Controllers;

use App\Traits\OrderCancelled;
use App\Traits\OrderPaid;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use App\Product;
use App\Entities\Tercero;
use App\Customer;
use DB;
use App\Logorder;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use MP;
use MercadoPagoException;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;
use App\Variant;
use App\LineItems;
use App\Commision;
use App\Tipo;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;


class OrdersController extends Controller
{
    use OrderPaid, OrderCancelled;

    public function listpaid()
    {
        return view('admin.orders.paid');
    }
    public function listpending()
    {
        return view('admin.orders.pending');
    }
    private function parseException($message)
    {
        $error = new \stdClass();
        $error->code = 0;
        $error->detail = '';
        $posA = strpos($message, '-');
        $posB = strpos($message, ':');
        if($posA && $posB) {
            $posA+=2;
            $length = $posB - $posA;
            // get code
            $error->code = substr($message, $posA, $length);
            // get message
            $error->detail = substr($message, $posB+2);
        }
        return $error;
    }
    public function paid()
    {

        ini_set('memory_limit','1000M');
        ini_set('xdebug.max_nesting_level', 120);
        ini_set('max_execution_time', 3000);
        $orders = Order::where('financial_status', 'paid')->where('cancelled_at', null)->get();
        $result = array();
        foreach ($orders as $order) {
            foreach ($order->line_items as $item) {
                if (isset($order->shipping_lines[0]['price'])){
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format($order->shipping_lines[0]['price']),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                } else {
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format(0),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                }
                array_push($result, $data);
            }
        }
        $send = collect($result);
        return Datatables::of($send)
            ->addColumn('nombre_producto', function ($send) {
                return '<div align=left>' . $send['nombre_producto'] . '</div>';
            })
            ->addColumn('numero_orden', function ($send) {
                return '<div align=left>' . $send['numero_orden'] . '</div>';
            })
            ->addColumn('precio_unidad', function ($send) {
                return '<div align=left>' . $send['precio_unidad'] . '</div>';
            })
            ->addColumn('cantidad', function ($send) {
                return '<div align=left>' . $send['cantidad'] . '</div>';
            })
            ->addColumn('costo_envio', function ($send) {
                return '<div align=left>' . $send['costo_envio'] . '</div>';
            })
            ->addColumn('fecha_compra_cliente', function ($send) {
                return '<div align=left>' . Carbon::parse($send['fecha_compra_cliente'])->toFormattedDateString() . '</div>';
            })
            ->addColumn('total', function ($send) {
                return '<div align=left>' . $send['total'] . '</div>';
            })
            ->make(true);

    }
    public function pending()
    {
        ini_set('memory_limit','1000M');
        ini_set('xdebug.max_nesting_level', 120);
        ini_set('max_execution_time', 3000);
        $orders = Order::where('financial_status', 'pending')->where('cancelled_at', null)->get();
        $result = array();
        foreach ($orders as $order) {
            foreach ($order->line_items as $item) {
                if (isset($order->shipping_lines[0]['price'])){
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format($order->shipping_lines[0]['price']),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                } else {
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format(0),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                }
                array_push($result, $data);
            }
        }
        $send = collect($result);
        return Datatables::of($send)
            ->addColumn('nombre_producto', function ($send) {
                return '<div align=left>' . $send['nombre_producto'] . '</div>';
            })
            ->addColumn('numero_orden', function ($send) {
                return '<div align=left>' . $send['numero_orden'] . '</div>';
            })
            ->addColumn('precio_unidad', function ($send) {
                return '<div align=left>' . $send['precio_unidad'] . '</div>';
            })
            ->addColumn('cantidad', function ($send) {
                return '<div align=left>' . $send['cantidad'] . '</div>';
            })
            ->addColumn('costo_envio', function ($send) {
                return '<div align=left>' . $send['costo_envio'] . '</div>';
            })
            ->addColumn('fecha_compra_cliente', function ($send) {
                return '<div align=left>' . Carbon::parse($send['fecha_compra_cliente'])->toFormattedDateString() . '</div>';
            })
            ->addColumn('total', function ($send) {
                return '<div align=left>' . $send['total'] . '</div>';
            })
            ->make(true);
    }
    public function home()
    {
        return view('admin.orders.home');
    }
    public function edit($id)
    {
        $order = Order::find($id);
        return view('admin.orders.edit')->with([
            'order' => $order
        ]);
    }
    public function up(Request $request, $id)
    {
        if (isset($request['date']) && isset($request['tipo_orden']) && !isset($request['code']) && !isset($request['url']) && !isset($request['code_internacional'])) {

            $date = $request['date'];
            $order = Order::find($id);
            $order->tipo_orden = $request['tipo_orden'];
            $order->fecha_compra = Carbon::parse($date);
            $order->estado_orden = 'comprado';
            $order->bitacora = currentUser();
            $order->save();

            if ($order) {
                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();
                $res = $client->request('get', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {

                    if (count($fulfillments['fulfillments']) > 0) {
                        $ok = null;
                        foreach ($fulfillments['fulfillments'] as $fulfillment) {

                            if ($fulfillment['status'] == 'success') {
                                $ok = $fulfillment['id'] ;
                            }
                        }

                        if ($ok != null) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $ok .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "confirmed"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Compra realizada con exito.']);
                            }


                        } else {

                            $res = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "order_id" => $order->order_id
                                    )
                                )
                            ));

                            $create = json_decode($res->getBody(), true);

                            if ($res->getStatusCode() == 201) {

                                $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $create['fulfillment']['id'] .'/events.json', array(
                                        'form_params' => array(
                                            'event' => array(
                                                "status" => "confirmed"
                                            )
                                        )
                                    )
                                );

                                if ($event->getStatusCode() == 201) {
                                    return redirect()->back()->with(['success' =>'Compra realizada con exito.']);
                                }
                            }
                        }

                    } else {
                        $res = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json', array(
                            'form_params' => array(
                                'fulfillment' => array(
                                    "order_id" => $order->order_id
                                )
                            )
                        ));
                        $create = json_decode($res->getBody(), true);

                        if ($res->getStatusCode() == 201) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $create['fulfillment']['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "confirmed"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Compra realizada con exito.']);
                            }
                        }
                    }
                }

            }
        }

        if (!isset($request['date']) && !isset($request['tipo_orden']) && isset($request['code']) && !isset($request['url'])) {

            $code = $request['code'];
            $order = Order::find($id);
            $order->codigo_envio = $code;
            $order->fecha_envio_n = Carbon::now();
            $order->estado_orden = 'envio_nacional';
            $order->bitacora = currentUser();
            $order->save();


            if ($order && $order->tipo_orden == 'nacional') {

                $update = Order::find($order->id);
                $update->url_envio = 'http://www.enviacolvanes.com.co/Contenido.aspx?rastreo=' . $code;
                $update->save();

                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();

                $res = $client->request('get', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {
                    if (isset($fulfillments['fulfillments'][0]) && count($fulfillments['fulfillments'][0]) > 0 && $fulfillments['fulfillments'][0]['status'] == 'success') {

                        $fulfillment = $client->request('put', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "tracking_company" => "Envia",
                                        "tracking_number" => $update->codigo_envio,
                                        "tracking_url" => $update->url_envio
                                    )
                                )
                            )
                        );

                        if ($fulfillment->getStatusCode() == 200) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "out_for_delivery"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Código Nacional agregado con exito.']);
                            }
                        }
                    }
                }
            }

            if ($order && $order->tipo_orden != 'nacional') {

                $update = Order::find($order->id);
                $update->url_envio = 'http://www.enviacolvanes.com.co/Contenido.aspx?rastreo=' . $code;
                $update->save();

                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();

                $res = $client->request('get', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {
                    if (isset($fulfillments['fulfillments'][0]) && count($fulfillments['fulfillments'][0]) > 0 && $fulfillments['fulfillments'][0]['status'] == 'success') {

                        $fulfillment = $client->request('put', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "tracking_company" => "Envia",
                                        "tracking_number" => $update->codigo_envio,
                                        "tracking_url" => $update->url_envio
                                    )
                                )
                            )
                        );

                        if ($fulfillment->getStatusCode() == 200) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "out_for_delivery"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Código Nacional agregado con exito.']);
                            }
                        }
                    }
                }
            }
        }

        if (!isset($request['date']) && !isset($request['tipo_orden']) && !isset($request['code']) && isset($request['code_internacional']) && isset($request['url'])) {
            $code = $request['code_internacional'];
            $order = Order::find($id);
            $order->codigo_envio_internacional = $code;
            $order->url_envio = $request['url'];
            $order->fecha_envio_i = Carbon::now();
            $order->estado_orden = 'envio_internacional';
            $order->bitacora = currentUser();
            $order->save();

            if ($order && ($order->tipo_orden == 'internacional' || $order->tipo_orden == 'nacional/internacional')) {
                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();

                $res = $client->request('get', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {
                    if (isset($fulfillments['fulfillments'][0]) && count($fulfillments['fulfillments'][0]) > 0 && $fulfillments['fulfillments'][0]['status'] == 'success') {

                        $fulfillment = $client->request('put', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "tracking_number" => $order->codigo_envio_internacional,
                                        "tracking_url" => $order->url_envio
                                    )
                                )
                            )
                        );

                        if ($fulfillment->getStatusCode() == 200) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "in_transit"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Código Internacional agregado con exito.']);
                            }
                        }
                    }
                }

            }
        }
    }
    public function anyData()
    {
        ini_set('memory_limit','2000M');

        if (Auth::user()->hasRole('logistica') && !Auth::user()->hasRole('administrador')) {
            $orders = Order::where('financial_status', 'paid')
                ->where('cancelled_at', null)
                ->get();
            $send = collect($orders);
            return Datatables::of($send )
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $img = '';
                            foreach ($product['images'] as $image) {
                                if (count($image['variant_ids']) > 0) {
                                    if ($image['variant_ids'][0] == $item['variant_id'] && $product['id'] == $item['product_id']) {
                                        $img = $image['src'];
                                    }

                                } else {
                                    $img = $product['image']['src'];
                                }
                            }
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['name'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            <div class="media-left">
                                                                <img src="' . $img . '" class="media-object" style="width:60px">
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading">Precio unidad: ' . number_format($item['price']) . '</h4>
                                                                <p>Cantidad: ' . $item['quantity'] . '</p>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                        }
                    }
                    if (count($send->shipping_lines) > 0) {
                        foreach ($send->shipping_lines as $line) {
                            return '
                  
                            <div class="text-left">
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">'. $send->order_number .'</button>
                                <!-- Modal -->
                                <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                            </div>
                                            <div class="modal-body">
                                                   '.$result.'
                                                   <p>Costo Envio: '.number_format($line['price']) .'</p>
                                                   <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                   
                        ';
                        }
                    } else {
                        return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">'. $send->order_number .'</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                           '.$result.'
                                           <p>Costo Envio:  0</p>
                                           <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                        
                ';
                    }
                })
                ->addColumn('customer', function ($send) {
                    $customer = Customer::where('email', $send->email)->first();
                    $orden_sin = 'Orden sin cliente';
                    if (count($customer) > 0) {
                        return '<div align=left>' . $customer->first_name . '</div>';
                    } else {
                        return '<div align=left>'. $orden_sin .'</div>';
                    }
                })
                ->addColumn('email', function ($send) {
                    return '<div align=left>'. $send->email .'</div>';
                })
                ->addColumn('address', function ($send) {
                    return '<div align=left>'. $send->billing_address['address1'] . ', ' . $send->billing_address['city'] . ', ' .$send->billing_address['country'] . '</div>';
                })
                ->addColumn('phone', function ($send) {
                    $phone = str_replace(' ', '', $send->billing_address['phone']);
                    return '<div align=left>'. $phone .'</div>';
                })
                ->addColumn('value', function ($send) {
                    return '<div align=left>' . number_format($send->total_price) . '</div>';
                })
                ->addColumn('name', function ($send) {
                    return '<div align=left>'. $send->name . '</div>';
                })

                ->addColumn('financial_status', function ($send) {
                    return '<div align=left>' . $send->financial_status. '</div>';
                })
                ->addColumn('fecha_compra_cliente', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->created_at)->toFormattedDateString() . '</div>';
                })
                ->addColumn('fecha_compra', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</div>';
                })
                ->addColumn('tipo_orden', function ($send) {
                    return '<div align=left>' . $send->tipo_orden . '</div>';
                })
                ->addColumn('codigo_envio', function ($send) {
                    return '<div align=left>' . $send->codigo_envio . '</div>';
                })
                ->addColumn('codigo_envio_internacional', function ($send) {
                    return '<div align=left>' . $send->codigo_envio_internacional . '</div>';
                })
                ->addColumn('estado_orden', function ($send) {

                    if ( $send->tipo_orden == 'internacional' || $send->tipo_orden == 'nacional/internacional' ) {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_internacional") {
                            $state .= 'Internacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle" >3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                      <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }
                    if ($send->tipo_orden == 'nacional') {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }

                })
                ->make(true);
        }
        else {
            $orders = Order::where('financial_status', 'paid')
                ->where('cancelled_at', null)
                ->get();
            $send = collect($orders);
            return Datatables::of($send )
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $img = '';
                            foreach ($product['images'] as $image) {
                                if (count($image['variant_ids']) > 0) {
                                    if ($image['variant_ids'][0] == $item['variant_id'] && $product['id'] == $item['product_id']) {
                                        $img = $image['src'];
                                    }

                                } else {
                                    $img = $product['image']['src'];
                                }
                            }
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['name'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            <div class="media-left">
                                                                
                                                                <img src="' . $img . '" class="media-object" style="width:60px">
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading">Precio unidad: ' . number_format($item['price']) . '</h4>
                                                                <p>Cantidad: ' . $item['quantity'] . '</p>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                        }
                    }
                    if (count($send->shipping_lines) > 0) {
                        foreach ($send->shipping_lines as $line) {
                            return '
                  
                            <div class="text-left">
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                                <!-- Modal -->
                                <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                            </div>
                                            <div class="modal-body">
                                                   '.$result.'
                                                   <p>Costo Envio: '.number_format($line['price']) .'</p>
                                                   <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                   
                        ';
                        }
                    } else {
                        return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                           '.$result.'
                                           <p>Costo Envio:  0</p>
                                           <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                        
                ';
                    }
                })
                ->addColumn('customer', function ($send) {
                    $customer = Customer::where('email', $send->email)->first();
                    $orden_sin = 'Orden sin cliente';
                    if (count($customer) > 0) {
                        return '<div align=left>' . $customer->first_name . '</div>';
                    } else {
                        return '<div align=left>'. $orden_sin .'</div>';
                    }
                })
                ->addColumn('email', function ($send) {
                    return '<div align=left>'. $send->email .'</div>';
                })
                ->addColumn('address', function ($send) {
                    return '<div align=left>'. $send->billing_address['address1'] . ', ' . $send->billing_address['city'] . ', ' .$send->billing_address['country'] . '</div>';
                })
                ->addColumn('phone', function ($send) {
                    $phone = str_replace(' ', '', $send->billing_address['phone']);
                    return '<div align=left>'. $phone .'</div>';
                })
                ->addColumn('value', function ($send) {
                    return '<div align=left>' . number_format($send->total_price) . '</div>';
                })
                ->addColumn('name', function ($send) {
                    return '<div align=left>'. $send->name . '</div>';
                })
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['name'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            <div class="media-left">
                                                                <img src="' . $product['image']['src'] . '" class="media-object" style="width:60px">
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading">Precio unidad: ' . number_format($item['price']) . '</h4>
                                                                <p>Cantidad: ' . $item['quantity'] . '</p>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                        }
                    }
                    if (count($send->shipping_lines) > 0) {
                        foreach ($send->shipping_lines as $line) {
                            return '
                  
                            <div class="text-left">
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                                <!-- Modal -->
                                <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                            </div>
                                            <div class="modal-body">
                                                   '.$result.'
                                                   <p>Costo Envio: '.number_format($line['price']) .'</p>
                                                   <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                   
                        ';
                        }
                    } else {
                        return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                           '.$result.'
                                           <p>Costo Envio:  0</p>
                                           <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                        
                ';
                    }
                })
                ->addColumn('financial_status', function ($send) {
                    return '<div align=left>' . $send->financial_status. '</div>';
                })
                ->addColumn('fecha_compra_cliente', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->created_at)->toFormattedDateString() . '</div>';
                })
                ->addColumn('fecha_compra', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</div>';
                })
                ->addColumn('tipo_orden', function ($send) {
                    return '<div align=left>' . $send->tipo_orden . '</div>';
                })
                ->addColumn('codigo_envio', function ($send) {
                    return '<div align=left>' . $send->codigo_envio . '</div>';
                })
                ->addColumn('codigo_envio_internacional', function ($send) {
                    return '<div align=left>' . $send->codigo_envio_internacional . '</div>';
                })
                ->addColumn('estado_orden', function ($send) {

                    if ( $send->tipo_orden == 'internacional' || $send->tipo_orden == 'nacional/internacional' ) {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_internacional") {
                            $state .= 'Internacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle" >3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                      <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }
                    if ($send->tipo_orden == 'nacional') {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }

                })
                ->addColumn('action', function ($send ) {
                    if ($send->fecha_compra == null) {
                        return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center" style="width: 100%">Comprar</a></div>';
                    } else {
                        return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center" style="width: 100%">Envio</a></div>';
                    }

                })
                ->make(true);
        }

    }
    public function index()
    {
        return view('admin.orders.index');
    }
    public function orders()
    {
        $orders = Logorder::all();
        $send = collect($orders);
        return Datatables::of($send )
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send->id . '</div>';
            })
            ->addColumn('order_id', function ($send) {
                return '<div align=left>' . $send->name . '</div>';
            })
            ->addColumn('checkout_id', function ($send) {
                return '<div align=left>' . $send->checkout_id. '</div>';
            })
            ->addColumn('value', function ($send) {
                return '<div align=left>' . number_format($send->value) . '</div>';
            })
            ->addColumn('status_shopify', function ($send) {
                return '<div align=left>' . $send->status_shopify . '</div>';
            })
            ->addColumn('status_mercadopago', function ($send) {
                return '<div align=left>' . $send->status_mercadopago . '</div>';
            })
            ->addColumn('payment_method_id', function ($send) {
                return '<div align=left>' . $send->payment_method_id . '</div>';
            })
            ->make(true);
    }
    public function status_orders()
    {
        $orders = DB::table('logsorders')
            ->select(DB::raw('count(id) as number'), 'status_shopify', 'status_mercadopago', 'payment_method_id')
            ->groupBy('status_shopify', 'status_mercadopago', 'payment_method_id')
            ->get();
        $send = collect($orders);
        return Datatables::of($send )
            ->addColumn('number', function ($send) {
                return '<div align=left>' . $send->number . '</div>';
            })
            ->addColumn('status_shopify', function ($send) {
                return '<div align=left>' . $send->status_shopify . '</div>';
            })
            ->addColumn('status_mercadopago', function ($send) {
                return '<div align=left>' . $send->status_mercadopago. '</div>';
            })
            ->addColumn('payment_method_id', function ($send) {
                return '<div align=left>' . $send->payment_method_id . '</div>';
            })
            ->make(true);
    }
    public function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
    public function create()
    {

        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $response = Order::where('network_id', 1)
                ->where('name', $order['name'])
                ->where('order_id', $order['id'])
                ->where('shop', 'good')
                ->first();

            if ($order['cancelled_at'] != null || $order['cancel_reason'] != null) {

                if(count($response) == 0) {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->points;

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->points, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    Order::createOrder($order, 'good', $puntos, $tipo_orden);

                    $tipo_orden = '';

                }
            }

            if ($order['cancelled_at'] == null && $order['cancel_reason'] == null) {

                if(count($response) == 0) {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->percentage * $item['quantity'];

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->percentage, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    $order_create = Order::createOrder($order, 'good', $puntos, $tipo_orden);

                    $tipo_orden = '';

                    $this->OrderPaid($order, $order_create, $puntos);
                }
            }
        }
    }
    public function update()
    {
        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $result = Order::where('network_id', 1)
                ->where('name', $order['name'])
                ->where('order_id', $order['id'])
                ->where('shop', 'good')
                ->first();

            if ($order['cancelled_at'] != null && $order['financial_status'] != 'paid') {

                if (count($result) > 0) {

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $this->OrderCancelled($result, $order);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->points;

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->points, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    Order::createOrder($order, 'good', $puntos, $tipo_orden);
                    $tipo_orden = '';

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            if ($order['cancelled_at'] != null && $order['financial_status'] == 'paid') {

                if (count($result) > 0) {

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $this->OrderCancelled($result, $order);

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->points;

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->points, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    Order::createOrder($order, 'good', $puntos, $tipo_orden);

                    $tipo_orden = '';

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            if ($order['cancelled_at'] == null && $order['financial_status'] == 'paid') {

                if (count($result) > 0) {

                    // envio de notificaciones en esta parte. OJO

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderPaid($order, $update, $update->points);

                        return response()->json(['status' => 'order has been processed'], 200);

                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if ($update->cargue_puntos == null ) {

                            $this->OrderPaid($order, $update, $update->points);
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderPaid($order, $update, $update->points);

                        return response()->json(['status' => 'order has been processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderPaid($order, $update, $update->points);

                        return response()->json(['status' => 'order has been processed'], 200);

                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->percentage * $item['quantity'];

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->percentage, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    $order_create = Order::createOrder($order, 'good', $puntos, $tipo_orden);

                    $tipo_orden = '';

                    $this->OrderPaid($order, $order_create, $puntos);

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            if ($order['cancelled_at'] == null && $order['financial_status'] != 'paid') {

                if (count($result) > 0) {

                    // envio de notificaciones en esta parte. OJO

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderCancelled($result, $order);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->points;

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->points, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    Order::createOrder($order, 'good', $puntos, $tipo_orden);

                    $tipo_orden = '';

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            return response()->json(['status' => 'order not processed'], 200);
        }
    }
    public function payment()
    {
        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $result = Order::where('network_id', 1)
                ->where('name', $order['name'])
                ->where('order_id', $order['id'])
                ->where('shop', 'good')
                ->first();

            if ($order['cancelled_at'] == null && $order['financial_status'] == 'paid') {

                if (count($result) > 0) {

                    // envio de notificaciones en esta parte. OJO

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderPaid($order, $update, $update->points);

                        return response()->json(['status' => 'order has been processed'], 200);

                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if ($update->cargue_puntos == null ) {

                            $this->OrderPaid($order, $update, $update->points);
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderPaid($order, $update, $update->points);

                        return response()->json(['status' => 'order has been processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $this->OrderPaid($order, $update, $update->points);

                        return response()->json(['status' => 'order has been processed'], 200);

                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;
                    $puntos = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {

                        foreach ($order['line_items'] as $item) {

                            $v = Variant::where('id', $item['variant_id'])
                                ->where('shop', 'good')
                                ->where('product_id', $item['product_id'])
                                ->first();

                            if (count($v) > 0) {

                                $puntos = $puntos + $v->percentage * $item['quantity'];

                                $line_item = LineItems::where('line_item_id', $item['id'])
                                    ->where('shop', 'good')
                                    ->where('variant_id', $item['variant_id'])
                                    ->first();

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, $v->percentage, 'good');
                                }

                                $product = Product::find($item['product_id']);

                                if ($product->tipo_producto == 'nacional') {
                                    $n++;
                                }
                                if ($product->tipo_producto == 'internacional') {
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($i > 0 && $n > 0) {
                        $tipo_orden .= 'nacional/internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i > 0 && $n == 0) {
                        $tipo_orden .= 'internacional';
                        $i = 0;
                        $n = 0;
                    }
                    if ($i == 0 && $n > 0) {
                        $tipo_orden .= 'nacional';
                        $i = 0;
                        $n = 0;
                    }

                    $order_create = Order::createOrder($order, 'good', $puntos, $tipo_orden);

                    $tipo_orden = '';

                    $this->OrderPaid($order, $order_create, $puntos);

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            return response()->json(['status' => 'order not processed'], 200);
        }
    }
    public function cancelled()
    {
        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $result = Order::where('network_id', 1)
                ->where('name', $order['name'])
                ->where('order_id', $order['id'])
                ->where('shop', 'good')
                ->first();

            if (count($result) > 0) {

                if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                    $update = Order::find($result->id);
                    $update->closed_at = $order['closed_at'];
                    $update->cancelled_at = $order['cancelled_at'];
                    $update->cancel_reason = $order['cancel_reason'];
                    $update->financial_status = $order['financial_status'];
                    $update->updated_at = Carbon::parse($order['updated_at']);
                    $update->save();

                    $this->OrderCancelled($result, $order);

                    return response()->json(['status' => 'order processed'], 200);

                }

                if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                    $update = Order::find($result->id);
                    $update->closed_at = $order['closed_at'];
                    $update->cancelled_at = $order['cancelled_at'];
                    $update->cancel_reason = $order['cancel_reason'];
                    $update->financial_status = $order['financial_status'];
                    $update->updated_at = Carbon::parse($order['updated_at']);
                    $update->save();

                    return response()->json(['status' => 'order processed'], 200);
                }

                if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                    $update = Order::find($result->id);
                    $update->closed_at = $order['closed_at'];
                    $update->cancelled_at = $order['cancelled_at'];
                    $update->cancel_reason = $order['cancel_reason'];
                    $update->financial_status = $order['financial_status'];
                    $update->updated_at = Carbon::parse($order['updated_at']);
                    $update->save();

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }

                if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                    $update = Order::find($result->id);
                    $update->closed_at = $order['closed_at'];
                    $update->cancelled_at = $order['cancelled_at'];
                    $update->cancel_reason = $order['cancel_reason'];
                    $update->financial_status = $order['financial_status'];
                    $update->updated_at = Carbon::parse($order['updated_at']);
                    $update->save();

                    return response()->json(['status' => 'order processed'], 200);
                }

                return response()->json(['status' => 'order not processed'], 200);

            } else {

                $tipo_orden = '';
                $i = 0;
                $n = 0;
                $puntos = 0;

                if (isset($order['line_items']) && count($order['line_items']) > 0) {

                    foreach ($order['line_items'] as $item) {

                        $v = Variant::where('id', $item['variant_id'])
                            ->where('shop', 'good')
                            ->where('product_id', $item['product_id'])
                            ->first();

                        if (count($v) > 0) {

                            $puntos = $puntos + $v->points;

                            $line_item = LineItems::where('line_item_id', $item['id'])
                                ->where('shop', 'good')
                                ->where('variant_id', $item['variant_id'])
                                ->first();

                            if (count($line_item) == 0) {

                                LineItems::createLineItem($item, $order, $v->points, 'good');
                            }

                            $product = Product::find($item['product_id']);

                            if ($product->tipo_producto == 'nacional') {
                                $n++;
                            }
                            if ($product->tipo_producto == 'internacional') {
                                $i++;
                            }
                        }
                    }
                }

                if ($i > 0 && $n > 0) {
                    $tipo_orden .= 'nacional/internacional';
                    $i = 0;
                    $n = 0;
                }
                if ($i > 0 && $n == 0) {
                    $tipo_orden .= 'internacional';
                    $i = 0;
                    $n = 0;
                }
                if ($i == 0 && $n > 0) {
                    $tipo_orden .= 'nacional';
                    $i = 0;
                    $n = 0;
                }

                Order::createOrder($order, 'good', $puntos, $tipo_orden);
                $tipo_orden = '';

                return response()->json(['status' => 'The resource is created successfully'], 200);
            }

        }
    }
    public function contador()
    {

    }
}