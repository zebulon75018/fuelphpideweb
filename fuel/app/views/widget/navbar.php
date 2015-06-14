        <!-- .navbar -->
        <nav class="navbar navbar-inverse navbar-static-top">
          <div class="container-fluid">

            <!-- Brand and toggle get grouped for better mobile display -->
            <header class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span> 
                <span class="icon-bar"></span> 
                <span class="icon-bar"></span> 
                <span class="icon-bar"></span> 
              </button>
              <a href="index.html" class="navbar-brand">
                <img src="/fuelphp/public/assets/img/logo.png" alt="">
              </a> 
            </header>
<?php
//echo View::forge('widget/toolbar')->render();
?>
            <div class="collapse navbar-collapse navbar-ex1-collapse">

              <!-- .nav -->
              <ul class="nav navbar-nav">

<?php

           function recursive_menu( $obj ,$deep)
           {
             if ( is_array($obj) ) {
                    if ( array_key_exists ("children",$obj) && is_array($obj["children"]) && count($obj["children"])>0) {

                        if ($deep > 0)
                        {
                        echo '<li class="dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$obj["name"].'</a> <ul class="dropdown-menu  multi-level" >';
                        }
                        else
                        {
                                echo '<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$obj["name"].'<b class="caret"></b></a> <ul class="dropdown-menu" >';
                        }

                        foreach( $obj["children"] as $m )
                                {
                                        recursive_menu($m , $deep +1);
                                }

                         echo "</ul>\n </li> \n";
                          }
                        else {
                           if ( array_key_exists ("isdivider",$obj) && $obj["isdivider"] == "true")
                           {
                             echo  "<li class='divider'></li>";
                           }
                           else
                           {
			    if ( array_key_exists("url", $obj) ) {
                         	echo "<li><a href='".$obj["url"]."'>".$obj["name"]."</a></li>\n";
				}
                          }
                        }
         	}
        }


        $jsonfiles =glob( APPPATH."classes/model/json/*.json" );
        $menucrud = array();
        $menujson = array();
        foreach( $jsonfiles as $t ) {
		$name = str_replace(APPPATH."classes/model/json/","",$t);
		$name = str_replace(".json","",$name);
	    	$menucrud[] = array("name"=>$name,"url"=>"/fuelphp/public/crud/view/".$name);
	    	$menujson[] = array("name"=>$name,"url"=>"/fuelphp/public/model/view/".$name);
	    	$menufilter[] = array("name"=>$name,"url"=>"/fuelphp/public/model/filter/".$name);
	}

        $classdir =scandir ( APPPATH."classes/" );
	$menuclass = array();
	foreach( $classdir as $c ) {
		if ($c=="..") continue;
		$menuclass[] = array("name"=>$c,"url"=>"/fuelphp/public/diagram/view/".$c);
	}

        $menuwidget = array();
	$menuwidget[] = array("name"=>"test","url"=>"/fuelphp/public/widget/test");
	$menuarray = array(
			//array ( "name"=>"test1","url"=>"#"),
			array ( "name"=>"Model","url"=>"/fuelphp/public/model" ),
			array ( "name"=>"Controller","url"=>"/fuelphp/public/controller" ),
			array ( "name"=>"Admin","children"=> $menucrud ),
			array ( "name"=>"Json","children"=> $menujson ),
			array ( "name"=>"Filter","children"=> $menufilter ),
			array ( "name"=>"Diagram","children"=> $menuclass ),
			array ( "name"=>"Widget","children"=> $menuwidget )
			//array ( "name"=>"test","children"=> array(array("name"=>"test2","url"=>"#")) )
			);
	foreach( $menuarray as $m )
	{
	   recursive_menu($m,0);
	}
        ?>

              </ul><!-- /.nav -->
            </div>
          </div><!-- /.container-fluid -->
        </nav><!-- /.navbar -->
        <header class="head">
