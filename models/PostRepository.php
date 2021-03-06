<?php
/**
 * Classe model de postagens  de  fatos e notícias
 * User: franc
 * Date: 29/11/18
 * Time: 17:51
 */

class PostRepository extends Post
{
    private $dados;
    private $ajax_error;
    public  $DirTiny;

    /**
     * Verifica e cria o diretório padrão de uploadsdo TinyMCE no sistema!<br>
     * <b>../imageTinymce/</b>
     */
    public function dirTiny($DirTiny = null) {

        $DirTiny = ((string) $DirTiny ? $DirTiny : '/imageTinymce/');
        if (!file_exists($DirTiny) && !is_dir($DirTiny)):
            mkdir($DirTiny, 0777);
        endif;
    }


    //metodo de cadastro no bando incluir a galeria
    public function addPost($dados_Form)
    {
        $dados_Form['cap_post'] = (!empty($_FILES['cap_post']['name']) ? $_FILES['cap_post'] : null);
        if($dados_Form['cap_post'] != null || !empty($_FILES['cap_post']['name'])){
            $this->title_exists($dados_Form['title_post']);

            $dados_Form['gall_post'] = (!empty($_FILES['gall_post']) ? $_FILES['gall_post'] : NULL);
            $dados_Form['type_post'] = 'post';
            $dados_Form['slug_post'] = Check::Name($dados_Form['title_post']);


            $Upload = new Upload('../uploads/');
            $Upload->Image($dados_Form['cap_post'], Check::Name($dados_Form['title_post']), 1600, 'posts');
            $dados_Form['cap_post'] = $Upload->getResult();

            // adiciona os dados no banco
            return $this->addposts($dados_Form);

        }else{
            $this->dados['retorno'] = Alert::AjaxDanger("<b>Erro ao enviar img  de capa!!</b>");
            $this->return_ajax_error($this->dados);
        }

    }

    /*Atualiza o post e sua galeria com todas as novas informações adicionadas*/
    public function upPosts($dados_Form)//1
    {
        $dados_Form['gall_post'] =  $_FILES['gall_post'];
        $dados_Form['type_post'] = 'post';
        $dados_Form['slug_post'] = Check::Name($dados_Form['title_post']);
        $dados_Form['cap_post']  = (!empty($_FILES['cap_post']['name']) ? $_FILES['cap_post'] : NULL);

        if(!empty($dados_Form['gall_post'])) {
            //com galeria
            $gallery = new GalleryRepository();
            $gallery->addGalery($dados_Form, $dados_Form['id_post']);
        }else{
            //sem galeria
            unset($dados_Form['gall_post']);
        }
        //verifica se o usuario quer mater ou não o mesmo titulo
        if($dados_Form['title_exist'] == '0') {
            $this->coverSend($dados_Form);//
        }else{
            $this->dados['retorno'] = Alert::AjaxInfo("<b>Esse Titulo <ins>{$dados_Form['title_post']}</ins> já existe.
                <p>
                <button class='btn btn-primary manterTitlePost'>Manter</button>
                <button class='btn btn-warning mudarTitlePost'>Mudar</button>
                </p>");
            $this->return_ajax_error($this->dados);
        }

        return true;
    }
//metodo fazo upload no tinyMCE
    public function imgContent($imgCont, $idPopst)
    {
        $nomeImag = "Img-Content";
        $Upload = new Upload('../uploads/');
        $Upload->Image($imgCont, "post-". $idPopst . "-" . $nomeImag, 1600, 'posts');
        $imgCont = $Upload->getResult();

        return $imgCont;
    }


    ############################  PRIVATES   ############################

    /*envia uma nova capa do post apagando a antiga*/
    private function coverSend(array $dados_Form)
    {
        $idPost = $dados_Form['id_post'];
        //reenvio da capa do post
        if (is_array($dados_Form['cap_post'])) {
            $capa = $this->SelcoverPost($idPost);

            $coverpost = "../uploads/" . $capa['cover_post'];
            if (file_exists($coverpost) && !is_dir($coverpost)) {
                unlink($coverpost);
            }
            $uploadCapa = new Upload('../uploads/');
            $uploadCapa->Image($dados_Form['cap_post'], $dados_Form['slug_post'], 1600, 'posts');

            if (isset($uploadCapa) && $uploadCapa->getResult()) {
                $dados_Form['cap_post'] = $uploadCapa->getResult();
                $this->updatePosts($dados_Form);//3
            }
        }else{
            unset($dados_Form['cap_post']);
            $this->updatePosts($dados_Form);//3

        }
    }

    public function excluiImgErro($img)
    {
        if (is_array($img)) {
            $coverpost = "../uploads/" . $img;
            if (file_exists($coverpost) && !is_dir($coverpost)) {
                unlink($coverpost);
            }
        }
    }
    //retorna  se o titulo do post estiver vazio e existir  outro com mesmo nome
    public function title_exists($dados_Form)
    {
        if ($this->setpost($dados_Form) == true) {
            $this->dados['retorno'] = Alert::AjaxDanger("<b>Esse Titulo <ins>{$dados_Form}</ins> já existe.
                <p>
                <button class='btn btn-primary manterTitlePost'>Manter</button>
                <button class='btn btn-warning mudarTitlePost'>Mudar</button>
                </p>");
            $this->return_ajax_error($this->dados);
        }
    }
    //retorna msg se tivercampos em branco
    private function is_empty(array $dados_Form)
    {
        if (in_array('', $dados_Form)) {
            $this->dados['retorno'] = Alert::AjaxWarning("<b><ins>Campos em Branco:</ins></br>Por favor prenncha todos os campos!!</b>");
            $this->return_ajax_error($this->dados);
        }
    }


    //retorna resposta do ajax
    public function return_ajax_error($data)
    {
        echo json_encode($data);
        exit();
    }

}
