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

	
	// $supplier = $_POST['supplier'];
	// if($supplier != 0)
	// {
	// 	$cus1 = $conn->prepare("SELECT * FROM bs_supplier WHERE sup_id = '$supplier'");
	// 	$cus1->execute();
	// 	$cus1_data = $cus1->fetch();

	// 	$cust_state = "AND d.sup_id = '$supplier'";
	// 	$cust_label = $cus1_data['company_name'];
	// }else{
	// 	$cust_state = "";
	// 	$cust_label = "All";
	// }
	

$errorMessage = (isset($_GET['error']) && $_GET['error'] != '') ? $_GET['error'] : '&nbsp;'
?>		
<head>		
<title>Received Product Report</title>
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
			&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
			</td>
			<td>
				<h3>Received Crop Report</h3>
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
							<td class="tdlabel">Product</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Brand</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Qty Left</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Minimum Qty</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Qty Received</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Received By</td>
							<td width="20px;">&nbsp;</td>
							<td class="tdlabel">Date Received</td>
						
						</tr>
			<tr>
				<td colspan="17"><hr color='black' /></td>
			</tr>
								  <tbody>
									<?php
										$emp = $conn->prepare("SELECT * FROM tbl_product p, tbl_received_product d
																	WHERE d.pd_id = p.pd_id
																			AND d.date_added_ymd BETWEEN '$newfrom' and '$newto'
																					ORDER BY d.date_added_ymd");
										$emp->execute();

										if($emp->rowCount() > 0)
										{
											$ctr1 = 1;
											while($emp_data = $emp->fetch())
											{
												$datereceived = date("M d, Y | h:i A", strtotime($emp_data['date_added']));
												
												$rby = $conn->prepare("SELECT * FROM bs_user WHERE user_id = '$emp_data[added_by]'");
												$rby->execute();
												
												if($rby->rowCount() > 0)
												{ 
													$rby_data = $rby->fetch();
													$received_by = utf8_encode(ucwords(strtolower($rby_data['lastname']))) . ',&nbsp;' . ucwords(strtolower($rby_data['firstname'])); 
												}else{ $received_by = '- -'; }
												
												$cat = $conn->prepare("SELECT * FROM tbl_category WHERE cat_id = '$emp_data[cat_id]'");
												$cat->execute();

												if($cat->rowCount() > 0)
												{
													$cat_data = $cat->fetch();
													$brandname = $cat_data['cat_name'];
												}else{ $brandname = ""; }
									?>
												<tr>
													<td class="tddata" valign="top"><?php echo $ctr1++; ?>. </td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $emp_data['pd_name']; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $brandname; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $emp_data['pd_qty_left']; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $emp_data['pd_mqty']; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $emp_data['od_qty_added']; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $received_by; ?></td>
													<td width="20px;">&nbsp;</td>
													<td class="tddata" valign="top"><?php echo $datereceived; ?></td>
											
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
										<td colspan="10" align="center" style="text-decoration: underline; font-weight:bold">&nbsp;&nbsp;&nbsp;Metrelina G. Ricafort&nbsp;&nbsp;&nbsp;</td>

										<td colspan="10" align="center"  style="text-decoration: underline; font-weight:bold">&nbsp;&nbsp;&nbsp;Jo Ann A. Diosa&nbsp;&nbsp;&nbsp;</td>
									</tr>
									<tr style="border-top: 1px;">
										<td colspan="10" align="center">Rice Program Coordinator</td>

										<td colspan="10" align="center">CGADH-I</td>
									</tr>
					  </tbody>
			</table>            
		</td></tr>
		</table>			