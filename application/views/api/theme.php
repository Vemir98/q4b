<?
$keys = ['Auth' => 'Авторизация','Companies' => 'Компании','Projects' => 'Проекты','Static' => 'Статичнеские данные'];
?>
<!DOCTYPE html>
<!-- saved from url=(0052) -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>Qforb JSON APIv1</title>

    <!-- Styles -->
    <link href="/media/apidoc/theDocs.all.min.css" rel="stylesheet">
    <link href="/media/apidoc/custom.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="/media/apidoc/css" rel="stylesheet" type="text/css">

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="http://thetheme.io/apple-touch-icon.png">
    <link rel="icon" href="http://thetheme.io/thedocs/assets/img/favicon.png">
  <link rel="stylesheet" crossorigin="anonymous" href="/media/apidoc/main.css"></head>

  <body data-spy="scroll" data-target=".sidebar" data-offset="200">


    <!-- Sidebar -->
    <aside class="sidebar sidebar-boxed sidebar-dark ps ps--theme_default" data-ps-id="15127b76-8605-7dcb-12b4-7fd0749a93b3">

        <a class="sidebar-brand" style="text-decoration: none!important;" href="<?=Request::current()->url()?>"><span style="font-size: 3em">Qforb JSON API</span>v1</h1></a>

      <ul class="nav sidenav dropable">
          <?foreach ($items as $key => $item):?>
              <li><a href="#<?=$key?>-0" class="has-child"><?=$keys[$key]?>
                      <ul>
                          <?foreach ($item as $k => $i):?>
                          <?if(!isset($i['title'])) continue;?>
                          <li><a href="#<?=$key.'-'.$k?>"><?=$i['title']?></a></li>
                          <?endforeach;?>
                      </ul>
              </a></li>
          <?endforeach;?>
      </ul>
</aside>
    <!-- END Sidebar -->


    <header class="site-header navbar-transparent">

      <!-- Top navbar & branding -->
      <nav class="navbar navbar-default">
        <div class="container-fluid">



            <button type="button" class="navbar-toggle for-sidebar" data-toggle="offcanvas">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>

          </div>
          <!-- END Toggle buttons and brand -->

        </div>
      </nav>
      <!-- END Top navbar & branding -->

      <!-- Banner -->
      <div class="banner auto-size" style="background-color: #5cc7b2">
        <div class="container-fluid text-white">
          <h1>Документация к <strong>Qforb JSON API</strong>v1</h1>
          <h5>Данный API разработан для доступа мобильных приложений к API ресурса qforb.net</h5>
        </div>
      </div>
      <!-- END Banner -->

    </header>

    <main class="container-fluid">

      <!-- Main content -->
      <article class="main-content" role="main">

        <p class="lead">Для того чтоб осуществлять запросы через предоставляемый интерфейс API, вы должны быть зарегистрированны на ресурсе qforb.net</p>


      <?foreach ($items as $key => $item):?>
          <?foreach ($item as $k => $i):?>
              <?if(!isset($i['title'])) continue;?>
              <section>
                  <?if(!$k):?>
                      <h2 id="<?=$key.'-'.$k?>"><a href="#<?=$key.'-'.$k?>"><?=$i['title']?></a></h2>
                  <?else:?>
                      <h3 id="<?=$key.'-'.$k?>"><a href="#<?=$key.'-'.$k?>"><?=$i['title']?></a></h3>
                  <?endif;?>
                  <p><?=$i['desc']?></p>
                  <h3>Запрос - <?=$i['method']?></h3>
                  <p><b>Адрес - </b><?=$i['url']?></p>
                  <p><b>Параметры - </b><?=$i['param']?></p>
<!--                  <p><b>Ответ - </b>--><?//=$i['return']?><!--</p>-->
                  <p><b>Коды ошибок - </b><?=str_ireplace('API_Exception','',$i['throws'])?></p>
                  <p><b>Пример ответа:</b></p>
                  <p><pre><code class="language-json"><?
                          try{
                              $file = 'api/responses/'.strtolower($key).'-'.$i['func'];
                              if (($path = Kohana::find_file('views', $file,'json')) !== FALSE)
                              {
                                  echo file_get_contents($path);
                              }
                          }catch (Exception $e){}
                  ?></code></pre></p>
              </section>
          <?endforeach?>
      <?endforeach;?>
        
      </article>
      <!-- END Main content -->
    </main>


    <!-- Footer -->
    <footer class="site-footer">
      <div class="container-fluid">
        <a id="scroll-up" href="#"><i class="fa fa-angle-up"></i></a>

        <div class="row">
          <div class="col-md-6 col-sm-6">
            <p>Copyright © <?=date('Y')?>. All right reserved</p>
          </div>
          <div class="col-md-6 col-sm-6">
            <ul class="footer-menu">
              <li></li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->

    <!-- Scripts -->
    <script src="/media/apidoc/theDocs.all.min.js"></script>
    <script src="/media/apidoc/custom.js"></script>

  

</body></html>