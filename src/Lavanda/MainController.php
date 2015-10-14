<?php
namespace Idealogica\Lavanda;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

/**
 * Main page controller.
 */
class MainController extends LaravelController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Displays main Lavanda page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('lavanda::main.index', ['title' => 'Control panel']);
    }
}
