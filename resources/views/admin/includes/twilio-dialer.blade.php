<div id="liveCallWidget" style="display: none;">
    <div class="row text-center">
        <span class="live-call-widget-signal" style="display: none;">
            <i class="fa fa-signal"></i>
        </span>
        <span class="live-call-widget-close" onclick="showHideDialer()">x</span>
        <div class="col-md-12 mb-10">
            <span class="live-call-status-text" id="call-status">
                Connecting...
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <a href="#" class="btn btn-icon round-btn btn-success answer-button"><i class="fa fa-phone-volume"></i></a>
        </div>
        <div class="col-md-6">
            <a href="#" class="btn btn-icon round-btn btn-danger hangup-button" onclick="hangUp()"><i class="fa fa-times"></i></a>
        </div>
    </div>
</div>