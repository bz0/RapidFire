<?php
    class RapidFire{
        private $timeout;
        private $running;
        private $mh;
        
        public function __construct($timeout){
            $this->timeout = $timeout;
        }
        
        /*
         * マルチリクエスト実行
         * @param array $urlList
         */
        public function shot($urlList){
            $this->mh = curl_multi_init();
            $this->handle_add($urlList);
            curl_multi_exec($this->mh, $this->running);
            $response = $this->responseWait();
            curl_multi_close($this->mh);
            
            return $response;
        }
        
        /*
         * マルチハンドル設定
         * @param array $url_info_list リクエスト情報
         */
        private function handle_add($url_info_list){
            foreach ($url_info_list as $url_info) {
                $ch = curl_init();
                $config = $this->set_curl_option($url_info);
                print_r($config);
                
                curl_setopt_array($ch, $config);
                curl_multi_add_handle($this->mh, $ch);
            }
        }
        /*
         * CURLのオプション設定
         * @param array $url_info (URL, CURLオプション)
         */
        private function set_curl_option($url_info){
            switch (gettype($url_info)){
                case 'string': 
                    $config = array(
                        CURLOPT_URL            => $url_info,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => $this->timeout,
                        CURLOPT_CONNECTTIMEOUT => $this->timeout
                    );
                    break;
                case 'array':
                    $config = array(
                        CURLOPT_URL            => $url_info['url'],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => $this->timeout,
                        CURLOPT_CONNECTTIMEOUT => $this->timeout
                    );

                    $config = $config + $url_info['curl_option'];
                    break;
                default:
                    break;
            }
            
            return $config;
        }
        
        /*
         * コンテンツ情報を取得
         * @param array $raised
         */
        private function response($raised){
            $info = curl_getinfo($raised['handle']);
            $content = curl_multi_getcontent($raised['handle']);
            
            return array(
                'status_code' => $info['http_code']
              , 'url'         => $info['url']
              , 'response'    => $content
            );
        }
        
        /*
         * 複数のコンテンツ情報を取得
         * @param array $response
         */
        private function get_multi_content($response){
            do{
                $raised = curl_multi_info_read($this->mh, $remains);

                if ($raised) {
                    $response[] = $this->response($raised);
                    curl_multi_remove_handle($this->mh, $raised['handle']);
                    curl_close($raised['handle']);
                }
            } while ($remains);
            
            return $response;
        }

        /*
         * レスポンスを待ってコンテンツ取得
         */
        private function responseWait(){
            $response = array();
            do {
                switch (curl_multi_select($this->mh, $this->timeout)){
                    case -1:
                        usleep(10);
                        curl_multi_exec($this->mh, $this->running);
                        break;
                    case 0:
                        break;
                    default:
                        curl_multi_exec($this->mh, $this->running);
                        $response = $this->get_multi_content($response);
                }
            } while ($this->running);
            
            return $response;
        }
    }