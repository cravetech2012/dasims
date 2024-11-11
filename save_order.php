<?php
require_once '../../global-library/config.php';
require_once '../../global-library/functions.php';
require_once '../../global-library/cart-functions.php';

checkUser();

$userId = $_SESSION['user_id'];

$today_date1 = date("Y-m-d H:i:s");
$today_date2 = date("Y-m-d");

$cartContent = getCartContent();
$numItem     = count($cartContent);
$rand_num = "00" . rand(10, 1000);

$mid = $_GET['mid'];
// $mid = $_POST['mid'];
$totalpcount = mysqli_real_escape_string($link, $_POST['totalpcount']);
$areatoplant = mysqli_real_escape_string($link, $_POST['areatoplant']);
$numofbag = mysqli_real_escape_string($link, $_POST['numofbag']);
// $cropvariety= mysqli_real_escape_string($link, $_POST['cropvariety']);
$cropestab = mysqli_real_escape_string($link, $_POST['cropestab']);
$exptsdate = mysqli_real_escape_string($link, $_POST['exptsdate']);
$datasharing = mysqli_real_escape_string($link, $_POST['datasharing']);

$exptsdate1 = 	date('F j, Y', strtotime($exptsdate));

$mon = $conn->prepare("SELECT * FROM tbl_monitoring WHERE m_id = '$mid'");
$mon->execute();
$mon_data = $mon->fetch();
$fid = $mon_data['fid'];


$sel = $conn->prepare("SELECT * FROM tbl_farmers WHERE fid = '$fid'");
$sel->execute();
$sel_data = $sel->fetch();

$area = $sel_data['area'];


$sid = session_id();
// save order & get order id
// $sql = $conn->prepare("INSERT INTO tbl_order(m_id, rand_number, od_date, od_date_1, released_by)
// 		VALUES ('$mid', '$rand_num',  '$today_date1', '$today_date2', '$userId')");
// $sql->execute();


// return $orderId;

// 	echo "<center><h3>Processing...</h3><img src='images/loader/loader_3.gif'><center>";
// 	$url = "index.php";
// 	echo "<meta http-equiv=\"refresh\" content=\"1;URL=$url\">";

if ($areatoplant <= $area) {

	/* Insert monitoring */
	$sql = $conn->prepare("UPDATE tbl_monitoring SET total_parcel_count = '$totalpcount', area_to_be_planted = '$areatoplant', number_of_bags = '$numofbag',
																 crop_estab = '$cropestab', expected_sowing_date = '$exptsdate1', 
																data_sharing_content = '$datasharing', date_released = '$today_date2', status = 'released' WHERE m_id = '$mid'");
	$sql->execute();
	/* End  */



	if ($mid) {
		// update product stock
		for ($i = 0; $i < $numItem; $i++) {
			$sql = $conn->prepare("UPDATE tbl_product 
						SET pd_qty = pd_qty - {$cartContent[$i]['ct_qty']}
						WHERE pd_id = {$cartContent[$i]['pd_id']}");
			$sql->execute();
		}

		// save order items
		for ($i = 0; $i < $numItem; $i++) {
			$prd = $conn->prepare("SELECT * FROM tbl_product WHERE pd_id = {$cartContent[$i]['pd_id']}");
			$prd->execute();
			$prd_data = $prd->fetch();
			$pdname = mysqli_real_escape_string($link, $prd_data['pd_name']);
			$bal_qty = $prd_data['pd_qty'];

			$sql = $conn->prepare("INSERT INTO tbl_order_item(m_id, pd_id, od_qty, od_price, od_cost, pd_qty_left, odi_bag)
						VALUES ($mid, {$cartContent[$i]['pd_id']}, {$cartContent[$i]['ct_qty']}, {$cartContent[$i]['ct_price']}, {$cartContent[$i]['ct_cost']}, '$bal_qty', {$cartContent[$i]['ct_bag']})");
			$sql->execute();
			// Order items id	
			$odi = $conn->lastInsertId();



			// save received to harvested list
			$sql = $conn->prepare("INSERT INTO tbl_harvested_list(m_id, pd_id, ct_session_id, hl_qty, hl_date, user_id)
					VALUES ('$mid', {$cartContent[$i]['pd_id']}, '$sid', {$cartContent[$i]['ct_bag']}, '$today_date1', '$userId')");
			$sql->execute();

		}

		// then remove the ordered items from cart
		for ($i = 0; $i < $numItem; $i++) {
			$sql = $conn->prepare("DELETE FROM tbl_cart
						WHERE ct_id = {$cartContent[$i]['ct_id']}");
			$sql->execute();
		}
		header("location: ../index.php?error=Released Successfully");
	}
} else {

	header("location: index.php?view=cart&mid=$mid&error=Area to be plant is More than Area registered for this Farmer!");
}
