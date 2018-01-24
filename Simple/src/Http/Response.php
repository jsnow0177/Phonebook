<?php
namespace Simple\Http;

class Response
{

    /**
     * @param mixed $data
     */
    public function Success($data)
    {
        $this->Output([
            'response' => $data
        ]);
    }

    /**
     * @param int    $code
     * @param string $message
     */
    public function Error(int $code = 0, string $message = '')
    {
        $this->Output([
            'error' => [
                'code'    => $code,
                'message' => $message
            ]
        ]);
    }

    /**
     * @param array $data
     */
    public function Output(array $data)
    {
        $content = json_encode($data);
        $length = mb_strlen($content, '8bit');

        header("Content-Type: application/json; charset=utf-8");
        header("Content-Length: " . $length);
        header("Pragma: no-cache");
        header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");

        echo $content;
        die();
    }

}