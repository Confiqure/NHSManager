				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-pencil fa-fw"></i> Record Hours
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form role="form" action="scripts/log_hours.php" method="POST">
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label>Service Date</label>
												<input class="form-control" type="date" name="date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Type of Service</label>
												<select class="form-control" name="service">
													<option selected>Community Service</option>
													<option>Tutoring</option>
												</select>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label>Hours</label>
												<input class="form-control" type="number" name="hours" min="0.25" max="20" step="0.25" value="1">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Supervisor</label>
												<input class="form-control" name="contact" placeholder="Provide contact info, just in case.">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label>Description</label>
										<textarea class="form-control" name="description" rows="3" placeholder="Use this section to talk a little about what you did to earn this service. The more detailed you are, the more likely it will be approved!"></textarea>
									</div>
									<button type="submit" class="btn btn-default">Request Approval</button>
								</form>
							</div>
							<!-- /.col-lg-12 (nested) -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-bar-chart-o fa-fw"></i> Hour Log History
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-striped">
										<thead>
											<tr>
												<th><center>Date</center></th>
												<th><center>Service</center></th>
												<th><center>Hours</center></th>
												<th><center>Description</center></th>
												<th><center>Approved?</center></th>
											</tr>
										</thead>
										<tbody>
											<?php
												if (strlen($account['pending']) === 0 && strlen($account['processed']) === 0) {
													echo '<tr><td colspan="5">No service documented.</td></tr>';
												} else {
													$types = array('pending', 'processed');
													for ($i = 0; $i < 2; $i++) {
														$entries = explode(';', $account[$types[$i]]);
														foreach ($entries as $entry) {
															if (strlen($entry) === 0) continue;
															$components = explode(',', $entry);
															$desc = $components[3];
															$desc = str_replace('/[c]/', ',', $desc);
															$desc = str_replace('/[s]/', ';', $desc);
															$desc = str_replace('/[q]/', '"', $desc);
															echo '
											<tr>
												<td style="vertical-align:middle"><center>' . $components[0] . '</center></td>
												<td style="vertical-align:middle"><center>' . ($components[1] === 'tutoring' ? 'Tutoring' : 'Community Service') . '</center></td>
												<td style="vertical-align:middle"><center>' . $components[2] . '</center></td>
												<td>' . $desc . '</td>
												<td style="vertical-align:middle"><center><i class="' . ($i === 0 ? 'glyphicon glyphicon-hourglass' : 'fa fa-' . $components[4] . ' fa-fw') . '"></i></center></td>
											</tr>' . "\n";
														}
													}
												}
											?>
										</tbody>
									</table>
								</div>
								<!-- /.table-responsive -->
							</div>
							<!-- /.col-lg-12 (nested) -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->