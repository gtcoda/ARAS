<?php
require_once($config['dirApiModules'] . 'apiClass.php');

class imgApi extends Api
{
    public $apiName = 'img';

    

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();
    }

    /**
     * Метод GET
     * 
     * @return string
     */
    public function indexAction()
    {

        $answer = array(
            'status' => 'success',
            'messages' => 'All gilds',
            'data' => "",
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод GET
     * 
     * @return string
     */
    public function viewAction()
    {

        $answer = array(
            'status' => 'error',
            'messages' => '',
        );
        return $this->response($answer, 400);
    }



    function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }


    /**
     * Метод POST
     */




    public function createAction()
    {



        $this->log->add($this->requestParams);

        // Если есть machine_id, значит фото относится к конкретной машине
        if(array_key_exists("machine_id", $this->requestParams)){

            $path = $this->config['dirIMG'].$this->requestParams['machine_id']."/";

            //создадим папку
            if (!file_exists($path)) { 
                mkdir($path);
            }
            
            // Соберем путь к файлу
            // Имя, метка времени unix
            $fileName = time().".jpg";

            $filePath = $path.$fileName;

            $this->base64_to_jpeg($this->requestParams['img'],$filePath);
            
            $url = [
                'url'=>"img/".$this->requestParams['machine_id']."/".$fileName,
            ];
            $answer = array(
                'status'    => 'success',
                'messages'  => "img upload",
                'data'      => $url
            );
    
            return $this->response($answer, 200);
    
        }
        $answer = array(
            'status' => 'error',
            'messages' => "",
        );

        return $this->response($answer, 400);
    }


    /**
     * Метод PUT
     * 
     * @return string
     */
    public function updateAction()
    {

        $answer = array(
            'status' => 'error',
            'messages' => "",
        );
        return $this->response($answer, 400);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/gilds/1
     * @return string
     */
    public function deleteAction()
    {


        $answer = array(
            'status' => 'error',
            'messages' => "",
        );

        return $this->response($answer, 400);
    }
}
