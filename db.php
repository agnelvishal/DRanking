
  <?php
  try {

    //Diagnosis
      //  ini_set("display_errors",1);
      //error_reporting(E_ALL);
    
      // Database details
      $d = "127.0.0.1";
      $u = "root";
      $p = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";
     

      $site = $_GET["site"];
      if ($site == "others") {
        $dbUsers = mysqli_connect($d, $u, $p, "avusers");
          echo '<div class="row" id="insert">';
          $siteText=$_GET["siteText"];
          $email=$_GET["email"];
          $email=mysqli_real_escape_string($dbUsers, $email);
          $siteText=mysqli_real_escape_string($dbUsers, $siteText);

          $item_select = "Insert into users (email, website) values ('".$email."','".$siteText."')";

          // echo $item_select;
          $result = mysqli_query($dbUsers, $item_select);
          if (!$result) {
              die('Some Problem : Blame agnelvishal@gmail.com -' . mysqli_error($dbUsers));
          } else {
              if ($email!=null && $siteText!=null) {
                  echo "You will receive the results of ".$siteText." in your mail ".$email." in around 2 hours";
              }
              if ($email!=null && $siteText==null) {
                  echo "You have not entered a website name";
              }
              if ($email==null && $siteText!=null) {
                  echo "Enter mail id above to get results of ".$siteText;
              }
              if ($email==null && $siteText==null) {
                  echo "You have to type the website you wish to see and your mail id in the above form";
              }
          }
          echo '</div>';
          mysqli_close($dbUsers);
      } else {

        //Db connection
        $db = mysqli_connect($d, $u, $p, "condense");
        if (mysqli_connect_errno($db)) {
            echo "AV:Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }

          $limitCount=$_GET["limit"];
          $limitStart=$_GET["limitStart"];
          $search=$_GET["search"];
          $advanced=$_GET["advanced"];
          $display=$_GET["display"];

          
          //$output=$_POST["output"];
    
          $dateSelect = $_GET["dateSelect"];
          if ($dateSelect == "custom") {
              $fromDate = $_GET["fromDate"];
              $toDate = $_GET["toDate"];
          } else {
              $fromDate = date("Y-m-d", strtotime($dateSelect));
              $toDate = date("Y-m-d", strtotime("now"));
          }


    
          $fromDate=mysqli_real_escape_string($db, $fromDate);
          $toDate=mysqli_real_escape_string($db, $toDate);
          $site=mysqli_real_escape_string($db, $site);
          $search=mysqli_real_escape_string($db, $search);
          $advanced=mysqli_real_escape_string($db, $advanced);
          $limitStart=mysqli_real_escape_string($db, $limitStart);
          $limitCount=mysqli_real_escape_string($db, $limitCount);
          $display=mysqli_real_escape_string($db, $display);

          //query generation for date
          $whereDateClause=" where";
          $whereDateClause.=" EthereumAddress is not null and ";
          $whereDateClause.="(date between\"";
          $whereDateClause.=$fromDate;
          $whereDateClause.="\" AND \"";
          $whereDateClause.=$toDate;
          $whereDateClause.="\" )";

if(!empty($search) and $advanced=="are")
{
    $whereDateClause.= "and (url like '%";
    $whereDateClause.= $search;
    $whereDateClause.= "%'";
    $whereDateClause.= "or keywords like '%";
    $whereDateClause.= $search;
    $whereDateClause.= "%'";
    $whereDateClause.= "or author like '%";
    $whereDateClause.= $search;
    $whereDateClause.= "%'";    
    $whereDateClause.= "or title like '%";
    $whereDateClause.= $search;
    $whereDateClause.= "%')";
}


          $item_select1 = "SELECT count(*) as count FROM `".$site."`".$whereDateClause;

          //echo $item_select;
          $result1 = mysqli_query($db, $item_select1);
          if (!$result1) {
              die('<div class="picked">Some problem : Consider sending a screenshot to agnelvishal@gmail.com </div>' . mysqli_error($db));
          }
          $rows1 = mysqli_fetch_assoc($result1);
          if ($rows1["count"]==0) {
              die('<div class="picked">No articles found. Try different</div>' . mysqli_error($db));
          }

          $item_select = "SELECT EthereumAddress,title,date, url,total,image,fblikes,fbshares,normMoz, reddit, pinterest FROM `".$site."`".$whereDateClause." ORDER BY total desc,fbshares desc limit ".$limitStart.",".$limitCount;

          //echo $item_select;
          $result = mysqli_query($db, $item_select);
          if (!$result) {
              die('Some Problem: Blame agnelvishal@gmail.com' . mysqli_error($db));
          }
          // Data being fetched for cards
          $table =  "<div class='picked'> Picked from ".number_format($rows1["count"])." articles in ".$site." </div>";
          
          if ($display=="cardView") {
              $table .= '<div class="row">';

              while ($rows = mysqli_fetch_assoc($result)) {
                  $table .=   '<div class="column"><div class="card">';

                  $table .='<a target="_blank" href="'.$rows["url"].'" ><img alt="Image" class="center-image" src="'.$rows["image"].'">';
                  $table .= '<p class="block-with-text"><span class="avtext">'.preg_replace('/u([a-fA-F0-9]{4})/', '&#x\\1;', $rows["title"]).'</span></p>';
                  $table .='<div class="container">';
                  $table .='<div class="totalPopularity"> '.number_format($rows["total"]).'</div>';
                  $table .= '<div class="meta">';
                  $table .= '<div class="meta-item searchEngine"><p class="label ">Search Engine Popularity:</p><p>';
                  $table .= number_format($rows["normMoz"])."</p></div>";
                  $table .= '<div class="meta-item fb"><p class="label ">Facebook Shares:</p><p>';
                  $table .= number_format($rows["fbshares"])."</p></div>";
                  $table .= '<div class="meta-item fb"><p class="label">Facebook Likes:</p><p>';
                  $table .= number_format($rows["fblikes"])."</p></div>";
                  $table .= '<div class="meta-item reddit"><p class="label">Reddit:</p><p>';
                  $table .= number_format($rows["reddit"])."</p></div>";
                  $table .= '<div class="meta-item pinterest"><p class="label">Pinterest:</p><p>';
                  $table .= number_format($rows["pinterest"])."</p></div>";
                  $table .= '</div>';
                  /*$table .= '<p class="description">';
                  $table .= $rows["description"];
                  $table .= '</p>';
                  */
                  $table.='</div></a></div><div onclick="sendToken(this)" class="upvote" data-address="'.$rows["EthereumAddress"].'"> Upvote</div></div>';
              }
              $table.='</div>';

              echo $table;
          }
else
{
          mysqli_data_seek($result, 0);
    include "chartsScript.php";

    // Data being fetched from db for charts
    $table = "";
    while ($rows = mysqli_fetch_assoc($result)) {
        if ($rows["normMoz"]==null) {
            continue;
        }
        $adate = date_create($rows["date"]);
        // Not sure why I am subtracting a month.
        date_sub($adate, date_interval_create_from_date_string('1 month'));

        $table .= '{';
        $table .= 'x:Date.UTC(';
        $table .= date_format($adate, 'Y,m,d').'),';
        $table .= 'y:';
        $table .= $rows["normMoz"].',';
        $table .= 'z:';
        $table .= $rows["total"].',';
        $table .= 'heading:';
        $table .= '\'';
        $table .=  addcslashes($rows["title"], "'");
        $table .= '\''.',';

        $table .= 'url:';
        $table .= '\'';
        $table .= $rows["url"];
        $table .= '\'';
        $table.='}';
        $table.=',';
    }
    echo $table; ?>

  ]}] });

 </script>

<?php
}
mysqli_free_result($result);
          mysqli_close($db);
      }
  } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
  }

  echo '<div class="made">Made by <a href="https://www.facebook.com/agnel.vishal" >Agnel Vishal</a>,<a href="https://www.facebook.com/domnic.amalan.7">  Domnic Amalan </a>, <a href="https://www.linkedin.com/in/joish-bosco-73389410b"> Joish </a>, <a href="https://www.facebook.com/rejo.jelestine">     Rejo.</a> </div>';
echo '<div class="made">Source code in <a href="https://github.com/agnelvishal/Condense.press"> Github:NewsDiet</a>.</div>';
echo '<div class="made"> Connect at <a href="https://www.facebook.com/Condensepress-367669160701798">Facebook Page</a></div>';
 
?>
