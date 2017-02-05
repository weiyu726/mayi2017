<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3 0003
 * Time: 下午 5:27
 */

namespace Admin\Model;
use Think\Model;
class ArticleModel extends Model
{
    protected $patchValidate = true; //开启批量验证
    /**
     * name   必填不能重复
     * status 可选0-1
     * sort   必须为数字
     * @var
     */
    protected $_validate = [
        ['name','require','分类名称不能为空'],
        ['article_category_id','require','分类名称不能为空'],
        ['name','','分类已存在',self::EXISTS_VALIDATE,'unique'],
        ['status','0,1','供货商状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数字'],
    ];

    /**
     * 获取分页数据和分页代码
     * @param array $cond 查询条件
     */
    public function getPageResult(array $cond=[]){
        //获取总行数
        //获取分页配置
        $page_setting = C('PAGE_SETTING');
        $count =$this->where($cond)->count();
        $page = new \Think\Page($count,$page_setting['PAGE_SIZE']);
        $page->setConfig('theme' ,$page_setting['PAGE_THEME']);
        $page_html = $page->show();
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return [
            'rows' => $rows,
            'page_html'=>$page_html,
        ];

    }
    public function addArticle(){
        //保存文章基本信息
        if(($article_id = $this->add()) ===false){
             return false;
        }
        //保存文章内容
        $data = [
            'article_id'=>$article_id,
            'content' =>I('post.content')
        ];
        $article_model = M('ArticleContent');
        if($article_model->add($data) === false){
            $this->error = '添加详细内容失败';
            return false;
        }
        return true;
    }

    public function saveArticle(){
        $article_id = $this->data['id'];
        //保存文章基本信息
        var_dump($article_id);
        if( $this->save() ===false){
            return false;
        }
        //保存文章内容
        $data = [
            'article_id'=>$article_id,
            'content' =>I('post.content')
        ];
        $article_model = M('ArticleContent');
        if($article_model->save($data) === false){
            $this->error = '添加详细内容失败';
            return false;
        }
        return true;
    }

    /**
     * 获取文章详细内容
     * @param $id
     * @return mixed
     */
    public function getArticleInfo($id){
        return $this->join('__ARTICLE_CONTENT__ as ac on ac.article_id  = __ARTICLE__.id')->find($id);
    }

    /**
     * 删除文章,包括详细内容
     * @param integer $id
     */
    public function deleteArticle($id){
        //删除基本信息
        if($this->delete($id) === false){
            return false;
        }
        //删除详细内容
        if(M('ArticleContent')->delete($id) === false){
            $this->error = '删除详细内容失败';
            return false;
        }
        return true;

    }
}