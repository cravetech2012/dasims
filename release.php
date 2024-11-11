<?php
require_once '../global-library/config.php';
require_once '../global-library/functions.php';

$_SESSION['login_return_url'] = $_SERVER['REQUEST_URI'];
checkUser();

	$userId = $_SESSION['user_id'];	
		
	$dfrom = $_POST['from'];
	$dto = $_POST['to'];	
	
	# Format Date to match date in db
	$newfrom = date("Y-m-d", strtotime($dfrom));
	$newto = date("Y-m-d", strtotime($dto));	
	# Format Date to words
	$wfrom = date("M d, Y", strtotime($dfrom));	
	$wto = date("M d, Y", strtotime($dto));
	
	// $product = $_POST['product'];
	// $category = $_POST['category'];
	// if($product != 0)
	// {
	// 	$cus1 = $conn->prepare("SELECT * FROM tbl_product WHERE pd_id = '$product'");
	// 	$cus1->execute();
	// 	$cus1_data = $cus1->fetch();
	// 	$cust_state = "AND oi.pd_id = '$product'";
	// 	$cust_label = $cus1_data['pd_name'];
	// }else{
	// 	$cust_state = "";
	// 	$cust_label = "All";
	// }
	
	// if($category != 0)
	// {		
	// 	$cat7_state = "AND p.cat_id = '$category'";		
	// }else{
	// 	$cat7_state = "";		
	// }
	
	// $customer = $_POST['customer'];
	// if($customer != 0)
	// {
	// 	$cust7 = $conn->prepare("SELECT * FROM bs_customer WHERE cust_id = '$customer'");
	// 	$cust7->execute();
	// 	$cust7_data = $cust7->fetch();
	// 	$cust_state7 = "AND o.cust_id = '$customer'";
	// 	$cust_label7 = $cust7_data['client_name'];
	// }else{
	// 	$cust_state7 = "";
	// 	$cust_label7 = "All";
	// }
	

$errorMessage = (isset($_GET['error']) && $_GET['error'] != '') ? $_GET['error'] : '&nbsp;'
?>		
<head>		
<title>Released Crop Report</title>
<link rel="shortcut icon" href="<?php echo WEB_ROOT; ?>images/favicon.ico">
<style rel="stylesheet">
.tdlabel
{   
   color: #000 !important;
   font-family: Arial !important;
   font-weight: bold;
   font-size:14px;
}
.tddata
{   
   color: #000 !important;
   font-family: Arial !important;  
   font-size:13px;
}
</style>
</head>
		<table style="margin:auto;">
		<tr>
			<td><img src="<?php echo WEB_ROOT; ?>images/logo/sasim.png" width="150px"/></td>
			<td>&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
			&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
			&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
			&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
			</td>
			<td>
				<h3>Released Crop Report</h3>
				<h4><?php echo $wfrom; ?> to <?php echo $wto; ?></h4>
			</td>
		</tr>
		<table>
		<br />
		<table style="margin:auto;">
		<tr><td>		
			<table style="padding:7px;">
			<tr>
							<td class="tdlabel">#</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Date Released</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Farmer</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Brand</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Product</td>
							<td width="20px;">&nbsp;</td>
						
							<td class="tdlabel">Qty</td>
							<td width="20px;">&nbsp;</td>
							
							<td class="tdlabel">Released By</td>
						</tr>
			<tr>
				<td colspan="17"><hr color='black' /></td>
			</tr>
								  <tbody>
									<?php
										$emp = $conn->prepare("SELECT * FROM tbl_monitoring m, tbl_order_item oi, tbl_product p
													WHERE m.m_id = oi.m_id AND oi.pd_id = p.pd_id 
															AND m.date_released BETWEEN '$newfrom' and '$newto'
																	ORDER BY m.date_released");
										$emp->execute();

										if($emp->rowCount() > 0)
										{
											$ctr1 = 1;
											while($emp_data = $emp->fetch())
											{
												

												$fId = $emp_data['fid'];
												
												$far = $conn->prepare("SELECT * FROM tbl_farmers WHERE fid = '$fId'");
												$far->execute();
												$far_data = $far->fetch();


												$farmerName = $far_data['lastname'] . ', ' . $far_data['firstname'] . ' ' . $far_data['middlename'];


												$datereleased = date("M d, Y | h:i a", strtotime($emp_data['date_received']));
												

												$rby = $conn->prepare("SELECT * FROM bs_user WHERE user_id = '$userId'");
												$rby->execute();

												if($rby->rowCount() > 0)
												{ 
													$rby_data = $rby->fetch();
													$released_by = utf8_encode(ucwords(strtolower($rby_data['lastname']))) . ',&nbsp;' . ucwords(strtolower($rby_data['firstname'])); 
												}else{ $released_by = '- -'; }
												
												$cat = $conn->prepare("SELECT * FROM tbl_category WHERE cat_id = '$emp_data[cat_id]'");
												$cat->execute();

												if($cat->rowCount() > 0)
												{
													$cat_data = $cat->fetch();
													$brandname = $cat_data['cat_name'];
												}else{ $brandname = ""; }
												
												$balance = $emp_data['pd_qty'] - $emp_data['od_qty'];
									?>
												<tr>
													<td class="tddata" valign="top"><?php echo $ctr1++; ?>. </td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $datereleased; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $farmerName; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $brandname; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $emp_data['pd_name']; ?></td>
													<td width="20px;">&nbsp;</td>
												
													<td class="tddata" valign="top"><?php echo $emp_data['od_qty']; ?></td>
													<td width="20px;">&nbsp;</td>
												
													<td class="tddata" valign="top"><?php echo $released_by; ?></td>
												</tr>
									<?php
											} // End While
										}else{}
									?>
								  <tr>
										<td colspan="17"><hr color='black' /></td>
									</tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr></tr>
									<tr style="border-top: 1px; margin-top: 20px;">
										<td colspan="6" align="center" style="text-decoration: underline; font-weight:bold">&nbsp;&nbsp;&nbsp;Metrelina G. Ricafort&nbsp;&nbsp;&nbsp;</td>

										<td colspan="10" align="center"  style="text-decoration: underline; font-weight:bold">&nbsp;&nbsp;&nbsp;Jo Ann A. Diosa&nbsp;&nbsp;&nbsp;</td>
									</tr>
									<tr style="border-top: 1px;">
										<td colspan="6" align="center">Rice Program Coordinator</td>

										<td colspan="10" align="center">CGADH-I</td>
									</tr>
					  </tbody>
			</table>            
		</td></tr>
		</table>			