<?php

namespace App\View\Components;

use App\Models\Report;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ContentReport extends Component
{
    public ?\App\Models\Post $postPreview = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public Report $report,
    ) {

        // if the report is about a post make a post model we can use for preview
        if (is_array($report->reported_content_snapshot ?? null)) {
            $this->postPreview = new \App\Models\Post($report->reported_content_snapshot);
            $this->postPreview->updated_at = $report->reported_content_snapshot['updated_at'] ?? null;
            $this->postPreview->load(['topic', 'author', 'editor']);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.content-report');
    }
}
