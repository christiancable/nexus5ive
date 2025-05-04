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

        // add previews for reported content
        if (is_array($report->reported_content_snapshot ?? null)) {

            switch (class_basename($report->reportable_type)) {
                case 'Post':
                    $this->postPreview = new \App\Models\Post($report->reported_content_snapshot);
                    $this->postPreview->updated_at = $report->reported_content_snapshot['updated_at'] ?? null;
                    $this->postPreview->load(['topic', 'author', 'editor']);
                    break;

                case 'Chat':
                    // @todo add sensible preview for chat
                    break;

                default:
                    // code...
                    break;
            }
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
