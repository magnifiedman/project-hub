<div class="row maincat-nav" style="display:none;">   
      
      <div class="col-sm-1 main">
      </div>

      <div class="col-sm-10 main">
        <div class="col-sm-4 main"><a href="javascript:void()" id="on-air-button" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-chevron-down on-air-icon"></span> On-Air</a></div>
        <div class="col-sm-4 main"><a href="javascript:void()" id="on-site-button" class="btn btn-default btn-block"><span class="glyphicon glyphicon-chevron-down on-site-icon"></span> On-Site</a></div>
        <div class="col-sm-4 main"><a href="javascript:void()" id="online-button" class="btn btn-default btn-block"><span class="glyphicon glyphicon-chevron-down online-icon"></span> Online / Other</a></div>
      </div>

      <div class="col-sm-1 main">
      </div>

    </div>
    

    <!-------------------->
    <!-- ON AIR SECTION -->
    <!-------------------->

    <div class="row on-air-form" style="display:none;">
        <div class="col-sm-1 main">
        </div>
        
        <div class="col-sm-10 main panel">
            
            
            <div class="col-sm-9">
              <h3>On-Air Details</h3>
            </div>
            <div class="col-sm-3">
            <p><label>&nbsp;</label><br />
            <a href="#" data-toggle="modal" data-target="#addFileOnAirModal" class="btn btn-default active btn-block"><span class="glyphicon glyphicon-plus"></span> Add Files</a>
            <?php $p->getFiles($projectID,1); ?>
          </p>
            
            </div>

            <div class="col-sm-12">
              <?php echo $oauText; ?>
            </div>
         
            <form action="#oau" role="form" id="" class="theForm" method="post">
            <input type="hidden" name="onAirUpdate" value="y" />
            <input type="hidden" name="id" value="<?php echo $projectID; ?>" />
            <input type="hidden" name="type_id" value="<?php echo $project['type_id']; ?>" />
            

            <!-- recorded inventory -->
            <div class="form-group clearfix">
                <label class="col-sm-3">&nbsp;<br />Recorded:</label>
                <div class="col-sm-4"> 
                  <div class="form-group">
                    Amount:
                    <div class="input-group">
                      <input type="number" name="recorded_amount" class="form-control" value="<?php echo $project['recorded_amount']; ?>" data-bv-digits-message><div class="input-group-addon">Units</div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-5">
                  Dates: <input name="recorded_dates" type="text" class="form-control" value="<?php echo $project['recorded_dates']; ?>" placeholder="">
                </div>
            </div>

            <!-- live inventory -->
            <div class="form-group clearfix">
              <label class="col-sm-3">&nbsp;<br />Live:</label>
                <div class="col-sm-4">
                  <div class="form-group">
                    Amount:
                    <div class="input-group">
                      <input type="number" name="live_amount" class="form-control" value="<?php echo $project['live_amount']; ?>" placeholder="" data-bv-digits-message><div class="input-group-addon">Units</div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-5">
                  Dates: <input name="live_dates" type="text" class="form-control" value="<?php echo $project['live_dates']; ?>" placeholder="">
                </div>
            </div>

            <!-- total inventory -->
            <div class="form-group clearfix">
              <label class="col-sm-3">&nbsp;<br />Total:</label>
                <div class="col-sm-9">
                  <div class="form-group">
                    Amount:
                    <div class="input-group">
                      <input type="number" name="total_amount" class="form-control" value="<?php echo $project['total_amount']; ?>" placeholder="" data-bv-digits-message><div class="input-group-addon">Units</div>
                    </div>
                  </div>
                </div>
            </div>

            <!-- giveaways -->
            <div class="form-group clearfix">
              <label class="col-sm-3">Giveaways:</label>
              <div class="col-sm-9">
                <textarea class="form-control" name="giveaways" placeholder=""><?php echo $project['giveaways']; ?></textarea>
              </div>
            </div>

            <div class="form-group clearfix">
              <div class="col-sm-12 clearfix">
              <span class="pull-right"><input type="submit" name="add" value="Update On-Air Details" class="btn btn-primary"></span>
              </div>
            </div>
            </form>

        </div>
       
        <div class="col-sm-1 main">  
        </div>
      </div>


    <!-------------------->
    <!-- ON SITE SECTION -->
    <!-------------------->

    <div class="row on-site-form" style="display:none;">
        <div class="col-sm-1 main">
        </div>
        
        <div class="col-sm-10 main panel">
            <div class="col-sm-9">
              <h3>On-Site Details</h3>
            </div>
            <div class="col-sm-3">
            <p><label>&nbsp;</label><br />
            <a href="#" data-toggle="modal" data-target="#addFileOnSiteModal" class="btn btn-default active btn-block"><span class="glyphicon glyphicon-plus"></span> Add Files</a>
            <?php $p->getFiles($projectID,2); ?>
            </p>
            </div>

            <div class="col-sm-12">
              <?php echo $osuText; ?>
            </div>

            <form action="#osu" role="form" id="" class="theForm" method="post">
            <input type="hidden" name="onSiteUpdate" value="y" />
            <input type="hidden" name="id" value="<?php echo $projectID; ?>" />
            <input type="hidden" name="type_id" value="<?php echo $project['type_id']; ?>" />
           
            
            <!-- event overview -->
            <div class="form-group clearfix">
              <label class="col-sm-3">&nbsp;<br />Overview:</label>
                <div class="col-sm-5">
                  <div class="form-group">
                    Event Date:
                      <input type="text" class="form-control datepicker" name="event_date" value="<?php echo $project['event_date']; ?>" placeholder="">
                  </div>
                </div>
                <div class="col-sm-4">
                  Event Time:<?php $z->timeSelect($project['event_time']); ?> 
                </div>  
            </div>

            <!-- talent -->
            <div class="form-group clearfix">
              <label class="col-sm-3">&nbsp;<br />Talent Requested:</label>
                <div class="col-sm-5">
                  <div class="form-group">
                    &nbsp;
                      <?php $z->talentSelect($project['talent_id']); ?> 
                  </div>
                </div>
                 <div class="col-sm-4"><div class="form-group">
                  Fee:
                  <div class="input-group">
                    <div class="input-group-addon">$</div><input type="number" name="talent_fee" class="form-control" value="<?php echo $project['talent_fee']; ?>" placeholder="" data-bv-digits-message></div>
                  </div>
               </div> 
            </div>

            <!-- techs -->
            <div class="form-group clearfix">
              <label class="col-sm-3">Techs Requested:</label>  
                <div class="col-sm-9">
                  <div class="form-group">              
                      <input type="text" class="form-control" name="techs_requested" value="<?php echo $project['techs_requested']; ?>" placeholder="">           
                  </div>
                </div>   
            </div>

            <!-- details -->
            <div class="form-group clearfix">
              <label class="col-sm-3">Details:</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="remote_details" placeholder="" rows="4"><?php echo $project['remote_details']; ?></textarea>
                </div>  
            </div>

            <!-- hard costs -->
            <div class="form-group clearfix">
              <label class="col-sm-3">&nbsp;<br />Hard Costs:</label>
                <div class="col-sm-4">
                  <div class="form-group">
                    Amount:
                    <div class="input-group">
                      <div class="input-group-addon">$</div><input type="number" name="hard_costs" value="<?php echo $project['hard_costs']; ?>" class="form-control" placeholder="" data-bv-digits-message>
                    </div>
                  </div>
                </div>
                <div class="col-sm-5">
                  Used For: <input type="text" class="form-control" name="used_for" value="<?php echo $project['used_for']; ?>" placeholder="">
                </div>
            </div>
      
            <div class="form-group clearfix">
              <div class="col-sm-12 clearfix">
              <span class="pull-right"><input type="submit" name="add" value="Update On-Site Details" class="btn btn-primary"></span>
              </div>
            </div>
            </form>

        </div>
       
        <div class="col-sm-1 main">  
        </div>
      </div>
      <!--  end on-site form -->


    <!-------------------->
    <!-- ONLINE SECTION -->
    <!-------------------->
    <div class="row online-form" style="display:none;">
        <div class="col-sm-1 main">
        </div>
        
        <div class="col-sm-10 main panel">
            <div class="col-sm-9">
            <h3>Online Details</h3>
            </div>
            <div class="col-sm-3">
            <p><label>&nbsp;</label><br />
            <a href="#" data-toggle="modal" data-target="#addFileOnlineModal" class="btn btn-default active btn-block"><span class="glyphicon glyphicon-plus"></span> Add Files</a>
            <?php $p->getFiles($projectID,3); ?>
            </p>
            </div>

            <div class="col-sm-12">
              <?php echo $oluText; ?>
            </div>

            <form action="#olu" role="form" id="" class="theForm" method="post">
            <input type="hidden" name="onLineUpdate" value="y" />
            <input type="hidden" name="id" value="<?php echo $projectID; ?>" />
            <input type="hidden" name="type_id" value="<?php echo $project['type_id']; ?>" />
            

            <h4>Advertising</h4>
            <div class="form-group clearfix">
              <div class="col-sm-3">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if(substr_count($project['ads'],'320x50')>0){ echo 'checked="checked"'; } ?> name="ads[]" value="320x50">320x50</label></p>
              </div>
              <div class="col-sm-3">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if(substr_count($project['ads'],'300x250')>0){ echo 'checked="checked"'; } ?> name="ads[]" value="300x250">300x250</label></p>
              </div>
              <div class="col-sm-3">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if(substr_count($project['ads'],'728x90')>0){ echo 'checked="checked"'; } ?> name="ads[]" value="728x90">728x90</label></p>
              </div>
              <div class="col-sm-3">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if(substr_count($project['ads'],'preroll')>0){ echo 'checked="checked"'; } ?> name="ads[]" value="preroll">Pre-Roll Video</label></p>
              </div>

            </div>
            <div class="form-group clearfix">
              <div class="col-sm-6">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if(substr_count($project['ads'],'hpt')>0){ echo 'checked="checked"'; } ?> name="ads[]" value="hpt">HPT</label><input type="text" name="hpt_dates" class="form-control" placeholder="Dates to Run" value="<?php echo $project['hpt_dates']; ?>" /></p>
              </div>
              
             <div class="col-sm-6">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if(substr_count($project['ads'],'eblast')>0){ echo 'checked="checked"'; } ?> name="ads[]" value="eblast">E-Blast</label><input type="text" name="eblast_dates" class="form-control" placeholder="Dates to Include" value="<?php echo $project['eblast_dates']; ?>" /></p>
              </div>

            </div>

            <div class="form-group clearfix">
              <div class="col-sm-12">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if($project['print_ad']=='y'){ echo 'checked="checked"'; } ?> name="print_ad" value="y">Print Ad</label><textarea name="print_ad_details" rows="9" class="form-control" placeholder="Enter details of ad here."><?php echo $project['print_ad_details']; ?></textarea></p>
              </div>

            </div>

            <h4>Social</h4>
              
            <div class="form-group clearfix"> 
              <div class="col-sm-4">
                <div class="form-group">
                 <strong><br />Facebook:</strong>
                   <input type="checkbox" <?php if($project['facebook']=='y'){ echo 'checked="checked"'; } ?> value="y" name="facebook" id="inlineCheckbox1">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  Amount:
                  <div class="input-group">
                    <input type="number" data-bv-digits-message="" placeholder="" class="form-control" name="facebook_amount" data-bv-field="facebook_amount" value="<?php echo $project['facebook_amount']; ?>"><div class="input-group-addon">Posts</div>
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                Post Dates:<input type="text" placeholder="" name="facebook_dates" class="form-control" value="<?php echo $project['facebook_dates']; ?>">
              </div>
            </div>

            <div class="form-group clearfix"> 
              <div class="col-sm-4">
                <div class="form-group">
                 <strong><br />Twitter:</strong>
                   <input type="checkbox" <?php if($project['twitter']=='y'){ echo 'checked="checked"'; } ?> value="y" name="twitter" id="inlineCheckbox1">
                </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group">
                  Amount:
                  <div class="input-group">
                    <input type="number" data-bv-digits-message="" placeholder="" class="form-control" name="twitter_amount" data-bv-field="twitter_amount" value="<?php echo $project['twitter_amount']; ?>"><div class="input-group-addon">Tweets</div>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                Post Dates:<input type="text" placeholder="" name="twitter_dates" class="form-control" value="<?php echo $project['twitter_dates']; ?>">
              </div>

            </div>     
         

            <h4>Web Elements</h4>
            <div class="form-group clearfix">
              <div class="col-sm-12">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if($project['dynamic_lead']=='y'){ echo 'checked="checked"'; } ?>v name="dynamic_lead" value="y">Dynamic Lead</label></p>
              </div>
            </div>

            <div class="form-group clearfix">
              <div class="col-sm-12">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if($project['custom_page']=='y'){ echo 'checked="checked"'; } ?> name="custom_page" value="y">Custom Page</label></p>
              </div>
              <div class="col-sm-12">
                <p><label>Custom Page Overview:</label><textarea class="form-control" name="custom_page_overview" rows="5"><?php echo $project['custom_page_overview']; ?></textarea></p>
              </div>
              <div class="col-sm-12">
                <p><label>Custom Page Copy:</label><textarea class="form-control" name="custom_page_copy" rows="5"><?php echo $project['custom_page_copy']; ?></textarea></p>
              </div>
            </div>

             <div class="form-group clearfix">
              <div class="col-sm-12">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if($project['contest_page']=='y'){ echo 'checked="checked"'; } ?> name="contest_page" value="y">Contest Page (Entry/Prizing)</label></p>
              </div>
              <div class="col-sm-12">
                <p><label>Contest Page Overview:</label><textarea class="form-control" name="contest_page_overview" rows="5"><?php echo $project['contest_page_overview']; ?></textarea></p>
              </div>
              <div class="col-sm-12">
                <p><label>Contest Page Copy:</label><textarea class="form-control" name="contest_page_copy" rows="5"><?php echo $project['contest_page_copy']; ?></textarea></p>
              </div>
            </div>

            <div class="form-group clearfix">
              <div class="col-sm-12">
                <p><label class="checkbox-inline"><input type="checkbox" <?php if($project['video']=='y'){ echo 'checked="checked"'; } ?> name="video" value="y">Custom Video</label></p>
              </div>
              <div class="col-sm-12">
                <p><label>Video Overview:</label><textarea class="form-control" name="video_overview" rows="5"><?php echo $project['video_overview']; ?></textarea></p>
              </div>
            </div>
            
            
            
            <div class="form-group clearfix">
              <div class="col-sm-12 clearfix">
              <span class="pull-right"><input type="submit" name="add" value="Update On-Site Details" class="btn btn-primary"></span>
              </div>
            </div>
          
            </form>

        </div>
       
        <div class="col-sm-1 main">  
        </div>
      </div>
      <!--  end online form -->