<div class="accordion mb-3" id="addTopic">
  <div class="card border-bottom">
    <div class="card-header bg-success text-white" id="addTopicHeading">
        <h2 class="h5 card-title mb-0">
            <a class="disclose text-white d-block" href="#" data-toggle="collapse" data-target="#addTopicForm" aria-expanded="false" aria-controls="addTopicForm">
                <span class="oi oi-chevron-right mr-2"></span>Add New Topic
            </a>
        </h2>
    </div>
    <div id="addTopicForm" class="collapse {{ ($errors->topicCreate->all()) ? "show" : "" }}" aria-labelledby="addTopicHeading" data-parent="#addTopic">
      <div class="card-body">
          @include('topics._create', $section)
      </div>
    </div>
  </div>
</div>

