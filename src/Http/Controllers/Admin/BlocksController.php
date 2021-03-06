<?php

namespace Whole\Core\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Whole\Core\Repositories\Block\BlockRepository;
use Whole\Core\Repositories\Role\RoleRepository;
use Whole\Core\Http\Requests\BlockRequest;
use Whole\Core\Repositories\Component\ComponentRepository;
use Whole\Core\Repositories\Content\ContentRepository;
use Whole\Core\Repositories\Page\PageRepository;
use Whole\Core\Repositories\Block\BlockDetailRepository;
use Illuminate\Support\Facades\Cache;
use Whole\Core\Logs\Facade\Logs;
class BlocksController extends Controller
{
    protected $block;
    protected $role;
    protected $component;
    protected $content;
    protected $page;
    protected $block_detail;


    /**
     * @param BlockRepository $block
     * @param BlockDetailRepository $block_detail
     * @param RoleRepository $role
     * @param ComponentRepository $component
     * @param ContentRepository $content
     * @param PageRepository $page
     */
    public function __construct(BlockRepository $block,BlockDetailRepository $block_detail, RoleRepository $role, ComponentRepository $component, ContentRepository $content, PageRepository $page)
    {
        $this->block = $block;
        $this->block_detail = $block_detail;
        $this->role = $role;
        $this->component = $component;
        $this->content = $content;
        $this->page = $page;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $blocks = $this->block->all();
        return view('backend::blocks.index',compact('blocks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = $this->role->all()->lists('role_name','id');
        return view('backend::blocks.create',compact('roles'));
    }


    /**
     * @param BlockRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlockRequest $request)
    {
        Cache::forget('_blocks');
        $data = $request->all();
        $data['access'] = serialize($data['access']);
        if ($block = $this->block->saveData('create',$data))
        {
            Logs::add('process',"Yeni Blok Eklendi \nBlok ID:{$block->id}");
            Flash::success('Başarıyla Kaydedildi');
            return redirect()->route('admin.block.index');
        }
        else
        {
            Logs::add('errors',"Blok Eklerken Hata Meydana Geldi! \n");
            Flash::error('Bir Hata Meydana Geldi ve Kaydedilemedi');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $block = $this->block->find($id);
        $pages = $this->page->all();
        $components = $this->component->allFile();
        $blocks = $this->block->exceptMe($id);
        $contents = $this->content->all();
        return view('backend::blocks.show',compact('id','block','components','blocks','contents','pages'))->with('slide_close',true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $block = $this->block->find($id);
        $roles = $this->role->all()->lists('role_name','id');
        return view('backend::blocks.edit',compact('block','roles'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param BlockRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BlockRequest $request, $id)
    {
        Cache::forget('_blocks');
        $data = $request->all();
        $data['access'] = serialize($data['access']);
        if ($this->block->saveData('update',$data,$id))
        {
            Logs::add('process',"Blok Başarıyla Düzenlendi \nBlok ID:{$id}");
            Flash::success('Başarıyla Düzenlendi');
            return redirect()->route('admin.block.index');
        }
        else
        {
            Logs::add('errors',"Blok Düzenlerken Hata Meydana Geldi \nBlok ID:{$id}");
            Flash::error('Bir Hata Meydana Geldi ve Düzenlenemedi');
            return redirect()->back();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Cache::forget('_blocks');
        $message = $this->block->delete($id) ?
            ['success','Başarıyla Silindi'] :
            ['error','Bir Hata Meydana Geldi ve Silinemedi'];
        if($message[0]=="success")
        {
            Logs::add('process',"Blok Başarıyla Silindi \nBlok ID:{$id}");
        }else
        {
            Logs::add('errors',"Blok Silinemedi \nBlok ID:{$id}");
        }
        Flash::$message[0]($message[1]);
        return redirect()->route('admin.block.index');
    }


    /**
     * @param Request $request
     * @return string
     */
    public function ajaxUpdate(Request $request)
    {
        Cache::forget('_blocks');
        return $this->block->find($request->get('id'))
            ->update([$request->get('type')=>$request->get('status')]) ?
            "true" :
            "false";
    }

    public function attributeCreate(Request $request,$id)
    {
        Cache::forget('_blocks');
        return $this->block_detail->create($request->all(),$id);
    }
}
