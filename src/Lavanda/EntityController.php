<?php
namespace Idealogica\Lavanda;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

/**
 * Default controller of Lavanda's entities.
 * Used for CRUD and list of items implementation.
 */
class EntityController extends Controller
{
    /**
     * Laravel request service instance.
     *
     * @var Request
     */
    protected $request = null;

    /**
     * Laravel session service instance.
     *
     * @var SessionManager
     */
    protected $session = null;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param SessionManager $session
     */
    public function __construct(Request $request, SessionManager $session)
    {
        parent::__construct();
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Displays list of items.
     *
     * @param string $model Model to use.
     * @return \Illuminate\Http\Response
     */
    public function index($model)
    {
        $this->setModel($model);
        $indexUrl = $this->getRoute('index');
        $title = trans(
            'lavanda::common.list_title',
            ['entity' => $this->staticModelGetPluralName()]);
        $columns = $this->staticModelGetListDescriptor();
        $sortDescriptor = $this->staticModelGetSortDescriptor();
        $searchForm = $this->staticModelGetSearchForm($indexUrl);
        $items = $this->staticModelGetList(
            $this->request->get('query'),
            getUnencryptedCookie('sort'))->
            setPath($indexUrl);
        return view('lavanda::entity.index', [
            'title' => $title,
            'columns' => $columns,
            'getRoute' => $this->getRoute(),
            'searchForm' => $searchForm,
            'sortDescriptor' => $sortDescriptor,
            'items' => $items,
            'createAllowed' => $this->isActionAllowed('create'),
            'editAllowed' => $this->isActionAllowed('edit'),
            'destroyAllowed' => $this->isActionAllowed('destroy')]);
    }

    /**
     * Shows item creation page.
     *
     * @param string $model Model to use.
     * @return \Illuminate\Http\Response
     */
    public function create($model)
    {
        $this->setModel($model);
        $this->checkAction('create');
        $title = trans(
            'lavanda::common.create_title',
            ['entity' => mb_strtolower($this->staticModelGetName())]);
        $form = $this->staticModelGetForm('post', $this->getRoute('store'));
        if(!$this->session->has('back_url'))
        {
            $this->session->flash(
                'back_url',
                $this->request->server('HTTP_REFERER'));
        }
        else
        {
            $this->session->reflash();
        }
        return view('lavanda::entity.create', [
            'title' => $title,
            'form' => $form]);
    }

    /**
     * Stores item created item.
     *
     * @param string $model Model to use.
     * @return \Illuminate\Http\Response
     */
    public function store($model)
    {
        $this->setModel($model);
        $this->checkAction('create');
        $form = $this->staticModelGetForm();
        if(!$form->isValid())
        {
            $this->session->reflash();
            return back()->
                withErrors($form->getErrors())->
                withInput()->
                with('msg', trans('lavanda::common.error_store'))->
                with('msg-type', 'danger');
        }
        $this->staticModelCreateInstance()->
            saveWithRelations($this->request->all());
        $redirectUrl = $this->session->get('back_url') ?
            $this->session->get('back_url') :
            $this->getRoute('index');
        return redirect()->
            to($redirectUrl)->
            with('msg', trans('lavanda::common.success_store'));
    }

    /**
     * Shows specified item.
     *
     * @param string $model Model to use.
     * @param int $id Item ID.
     * @return \Illuminate\Http\Response
     */
    public function show($model, $id)
    {
        $this->setModel($model);
        $item = $this->staticModelGetItem($id);
        if(!$item)
        {
            abort(404);
        }
        $title = trans(
            'lavanda::common.show_title',
            ['entity' => $this->staticModelGetName(), 'id' => $id]);
        $rows = $this->staticModelGetItemDescriptor();
        return view('lavanda::entity.show', [
            'title' => $title,
            'rows' => $rows,
            'getRoute' => $this->getRoute(),
            'item' => $item]);
    }

    /**
     * Shows the form for item editing.
     *
     * @param string $model Model to use.
     * @param int $id Item ID.
     * @return \Illuminate\Http\Response
     */
    public function edit($model, $id)
    {
        $this->setModel($model);
        $this->checkAction('edit');
        $item = $this->staticModelgetFormItem($id);
        if(!$item)
        {
            abort(404);
        }
        $title = trans(
            'lavanda::common.edit_title',
            ['entity' => mb_strtolower($this->staticModelGetName()), 'id' => $id]);
        $form = $this->staticModelGetForm(
            'put',
            $this->getRoute('update', ['id' => $id]),
            $this->session->hasOldInput() ? null : $item);
        if(!$this->session->has('back_url'))
        {
            $this->session->flash(
                'back_url',
                $this->request->server('HTTP_REFERER'));
        }
        else
        {
            $this->session->reflash();
        }
        return view('lavanda::entity.edit', [
            'title' => $title,
            'form' => $form]);
    }

    /**
     * Updates the specified item in storage.
     *
     * @param string $model Model to use.
     * @param int $id Item ID.
     * @return \Illuminate\Http\Response
     */
    public function update($model, $id)
    {
        $this->setModel($model);
        $this->checkAction('edit');
        $item = $this->staticModelFind($id);
        if(!$item)
        {
            abort(404);
        }
        $form = $this->staticModelGetForm();
        if(!$form->isValid())
        {
            $this->session->reflash();
            return back()->
                withErrors($form->getErrors())->
                withInput()->
                with('msg', trans('lavanda::common.error_update'))->
                with('msg-type', 'danger');
        }
        $item->saveWithRelations($this->request->all());
        $redirectUrl = $this->session->get('back_url') ?
            $this->session->get('back_url') :
            $this->getRoute('index');
        return redirect()->
            to($redirectUrl)->
            with('msg', trans('lavanda::common.success_update'));
    }

    /**
     * Removes the specified item from storage.
     *
     * @param string $model Model to use.
     * @param int $id Item ID.
     * @return \Illuminate\Http\Response
     */
    public function destroy($model, $id)
    {
        $this->setModel($model);
        $this->checkAction('destroy');
        $item = $this->staticModelFind($id);
        if(!$item)
        {
            abort(404);
        }
        $item->delete();
        $this->session->flash('msg', trans('lavanda::common.success_destroy'));
        return $this->getRoute('index');
    }
}
