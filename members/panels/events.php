				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-calendar fa-fw"></i> Upcoming Events<?php if ($account['role'] !== "Member") echo '&nbsp;&nbsp;&nbsp;(if any info below needs to be changed or removed, please <a href="mailto:ask@bownhs.org">email me</a>)'; ?>
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<?php
							echo '<ul class="timeline">';
							for ($i = 0; $i < sizeof($events); $i++) {
								if ($events[$i] == null) continue;
								$events[$i]['title'] = str_replace('/[c]/', ',', $events[$i]['title']);
								$events[$i]['title'] = str_replace('/[s]/', ';', $events[$i]['title']);
								$events[$i]['title'] = str_replace('/[q]/', '"', $events[$i]['title']);
								$events[$i]['description'] = str_replace('/[c]/', ',', $events[$i]['description']);
								$events[$i]['description'] = str_replace('/[s]/', ';', $events[$i]['description']);
								$events[$i]['description'] = str_replace('/[q]/', '"', $events[$i]['description']);
								$going = strpos($events[$i]['going'], $account['studentname']) !== false;
								echo '
							<li' . ($i % 2 == 1 ? ' class="timeline-inverted"' : '') . '>
								<div class="timeline-badge ' . $events[$i]['color'] . '"><i class="' . $events[$i]['icon'] . '"></i></div>
									<div class="timeline-panel">
										<div class="timeline-heading">
											<h4 class="timeline-title">' . $events[$i]['title'] . '</h4>
											<p><small class="text-muted"><i class="fa fa-clock-o"></i> ' . date('l, F j, Y', strtotime($events[$i]['date'])) . '</small></p>
										</div>
										<div class="timeline-body">
											<p>' . $events[$i]['description'] . '</p>
											<hr />
											<a type="button" class="btn btn-' . ($going ? 'danger' : 'success') . ' btn-sm" href="scripts/update_attendance.php?id=' . $events[$i]['id'] . '&going=' . !$going . '">
												' . ($going ? 'Remove your name' : 'Sign up') . '
											</a>
											<a type="button" class="btn btn-info btn-sm" href="event/' . $events[$i]['id'] . '">
												<span>Who\'s going?</span>
											</a>
										</div>
									</div>
								</li>' . "\n";
								}
							echo '</ul>';
						?>
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
