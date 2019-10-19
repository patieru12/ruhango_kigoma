<?php
/*session_start();
require_once "../lib/db_function.php";*/
$SchoolItem = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.*, 
																	carriedForward AS availableQuantity 
																	FROM ac_schoolstockitemtype AS a 
																	LEFT JOIN (
																		SELECT 	a.id AS id,
																				(COALESCE(b.receivedQuantity, 0) - COALESCE(c.distributedQuantity, 0)) AS carriedForward
																				FROM ac_schoolstockitemtype AS a
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS receivedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 0
																							GROUP BY b.id
																				) AS b
																				ON a.id = b.id
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS distributedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 1
																							GROUP BY b.id
																				) AS c
																				ON a.id = c.id
																	) AS d
																	ON a.id = d.id
																	", $con), true, $con);
// var_dump($SchoolItem);
?>
	<div class="modal fade" id="Model_AccountingStockReceive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
		<div class="modal-wrapper">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel2">Receive New Item</h4>
					</div>
					<div class="form-group" id="formAccountingStockReceiveLoading" style="display: none;">
						<center>
							<div class="row">
								<img src="./assets/img/loading.gif" alt="Loading..." height="35">
							</div>
							<div class="row" >
								<p>saving...</p>
							</div>
						</center>
					</div>
					<form class="form-horizontal" role="form" id="formAccountingStockReceive" >
						<div class="modal-body">
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Item Type</label>
									<div class="col-sm-7">
										<input name="receiveItemType" type='hidden' id='receiveItemType' class="form-control" placeholder="Select Received Item" />
										<!-- <select name="receiveItemType" class="form-control" id="receiveItemType" placeholder="Select Received Item">
											<option></option>
											@foreach ($DashData["Accounting"]["SchoolItem"] as $SchoolItem )
												<option value="{{ $SchoolItem->id }}" >
													{{$SchoolItem->name}}
												</option>
											@endforeach
										</select> -->
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Quantity</label>
									<div class="col-sm-7">
										<input type="text" name="receiveItemQuantity" class="form-control" id="receiveItemQuantity" placeholder="Enter Received Quantity" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Date</label>
									<div class="col-sm-7">
										<input type="text" name="receiveItemDate" class="form-control" id="receiveItemDate" placeholder="Enter action date" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Supplier</label>
									<div class="col-sm-7" id="accountingStockReceiveSupplier">
										<input name="receiveItemSupplier" type='hidden' id='receiveItemSupplier' class="form-control" placeholder="Select Item Supplier" />
										<!-- <select name="receiveItemSupplier" class="form-control" id="receiveItemSupplier" placeholder="Select Item Supplier">
											<option></option>
											@foreach ($DashData["Accounting"]["SchoolSupplier"] as $SchoolSupplier )
												<option value="{{ $SchoolSupplier->id }}" >
													{{$SchoolSupplier->name}}
												</option>
											@endforeach
										</select> -->
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Comment</label>
									<div class="col-sm-7">
										<input type="text" name="receiveItemComment" class="form-control" id="receiveItemComment" placeholder="Provide comment if any" />
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary" >Add</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="Model_AccountingStockDistribute" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
		<div class="modal-wrapper">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel2">Distribute an Item</h4>
					</div>
					<div class="form-group" id="formAccountingStockDistributeLoading" style="display: none;">
						<center>
							<div class="row">
								<img src="./assets/img/loading.gif" alt="Loading..." height="35">
							</div>
							<div class="row" >
								<p>saving...</p>
							</div>
						</center>
					</div>
					<form class="form-horizontal" role="form" id="formAccountingStockDistribute" >
						<div class="modal-body">
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Item Type</label>
									<div class="col-sm-7">
										<select name="distributeItemType" class="form-control" id="distributeItemType" placeholder="Select Distributed Item">
											<option></option>
											<?php
											foreach ($SchoolItem as $item ){
												?>
												<option value="<?= $item['id'] ?>" data-quantity="<?= $item['availableQuantity'] ?>">
													<?= $item['name'] ?>
												</option>
												<?php
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Quantity</label>
									<div class="col-sm-5">
										<input type="text" name="distributeItemQuantity" class="form-control" id="distributeItemQuantity" placeholder="Enter Distributed Quantity" />
									</div>
									<div class="col-sm-4 text-left text-red" id="distributeAvailableQuantity" >
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Date</label>
									<div class="col-sm-7">
										<input type="text" name="distributeItemDate" class="form-control" id="distributeItemDate" placeholder="Enter action date" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Receiver</label>
									<div class="col-sm-7">
										<input name="distributeItemReceiver" type='hidden' id='distributeItemReceiver' class="form-control" placeholder="Select Item Supplier" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="form-group">
									<label class="col-sm-3 control-label">Comment</label>
									<div class="col-sm-7">
										<input type="text" name="distributeItemComment" class="form-control" id="distributeItemComment" placeholder="Provide comment if any" />
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary" >Distribute</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>