    <?php

    /*
     * The MIT License
     *
     * Copyright 2015 aman.
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     */

    use SOURCEPATH\V1\Models\MainModel;
    use SOURCEPATH\V1\Models\NewsDbHandler;
    use SOURCEPATH\V1\Models\DbHandler;

    $app->post('/news/add', 'authenticate', function() use ($app) {
        // check for required params
        $result1 = MainModel::verifyRequiredParams(array('note'));
        if ($result1 != 'done') {
            MainModel::resultreached($app, $result1, true);
        }
        global $user_id;
        // reading post params
        $note = $app->request->post('note');
        if($app->request->post('image_url'))
         $url = $app->request->post('image_url');
        else
         $url='';
        $db = new NewsDbHandler();
        $userdb = new DbHandler();
        $name = $userdb->getUserByid($user_id);
        $res = $db->createNews($user_id,$note, $url,$name['name']);
        if ($res != 'ERROR-INSERTING') {
            MainModel::resultreached($app, 'News Updated==>'.$res, false);
        } else {
            MainModel::resultreached($app, $res, true);
        }
    });

    $app->post('/topic/add', 'authenticate', function() use ($app) {
        // check for required params
        $result1 = MainModel::verifyRequiredParams(array('note'));
        if ($result1 != 'done') {
            MainModel::resultreached($app, $result1, true);
        }
        global $user_id;
        // reading post params
        $note = $app->request->post('note');
        $db = new NewsDbHandler();
        $res = $db->AddTopic($user_id,$note);
        if ($res != 'ERROR-INSERTING') {
            MainModel::resultreached($app, 'Topics Sent==>'.$res, false);
        } else {
            MainModel::resultreached($app, $res, true);
        }
    });

//    $app->get('/news/all(/:startid)', function($startid=0) use ($app) {
//        $db=new NewsDbHandler();
//        $result=$db->getAllNews($startid);
//        if ($result["news"] == NULL) {
//            MainModel::resultreached($app,'Error Fetching News', TRUE);
//        } else {
//            $app->render(200, $result);
//        }
//    });
