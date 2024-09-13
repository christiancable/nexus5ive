<div class="accordion mb-3" id="addSection">
  <div class="card border-bottom">
    <div class="card-header bg-success text-white" id="addSectionHeading">
        <h2 class="h5 card-title mb-0">
            <a class="disclose text-white d-block" href="#" data-toggle="collapse" data-target="#addSectionForm" aria-expanded="false" aria-controls="addSectionForm">
                <span class="oi oi-chevron-right mr-2"></span>Add New Section
            </a>
        </h2>
    </div>
    <div id="addSectionForm" class="collapse {{ ($errors->sectionCreate->all()) ? "show" : "" }}" aria-labelledby="addSectionHeading" data-parent="#addSection">
      <div class="card-body">
          @include('nexus.sections._create', $section)
      </div>
    </div>
  </div>
</div>