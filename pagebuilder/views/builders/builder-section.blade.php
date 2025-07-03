@if (count($page_sections))
    @foreach ($page_sections as $section)
        @php
            $section_prop = !empty($section['properties']['properties']) ? $section['properties']['properties'] : false;
            $container = isset($section_prop['container']) ? $section_prop['container'] : 'container';
            $vertical = isset($section_prop['vertical']) ? $section_prop['vertical'] : 'start';
        @endphp
        <section class="pt-10 pb-10" id="section_{{ $section['id'] . '_' . $section['page_id'] }}">
            <div class="{{ $container }}">
                <div class="row align-items-{{ $vertical }}">

                    @foreach ($section['layouts'] as $section_layouts)
                        <div class="col-md-{{ $section_layouts['col_value'] }}">

                            @foreach ($section_layouts['layout_widgets'] as $layout_widget)
                                @php
                                    $properties = !empty($layout_widget['properties']['properties']) ? $layout_widget['properties']['properties'] : false;
                                    $alignment = isset($properties['alignment']) ? $properties['alignment'] : 'start';
                                    $widget_name = $layout_widget['widget']['name'];

                                    switch ($widget_name) {
                                        case 'author':
                                            $widget_specific_class = 'widget-about';
                                            break;

                                        case 'tag':
                                            $widget_specific_class = 'widget-tag-cloud';
                                            break;

                                        case 'list_blog':
                                            $widget_specific_class = 'widget-recent-post';
                                            break;

                                        case 'title_overlay_blog':
                                            $widget_specific_class = 'widget-featured-post';
                                            break;

                                        case 'slider_blog':
                                            $widget_specific_class = 'widget-most-commented-post';
                                            break;

                                        case 'review':
                                            $widget_specific_class = 'widget-most-commented-post';
                                            break;

                                        default:
                                            $widget_specific_class = '';
                                            break;
                                    }
                                @endphp

                                <div class="widget w-100 text-{{ $alignment }} {{ $widget_specific_class }} shadow-none"
                                    id="section_{{ $section['id'] . '_widget_' . $layout_widget['id'] }}">

                                    @if ($properties)
                                        @includeIf(
                                            'plugin/pagebuilder::builders.builder-widgets.' . $widget_name,
                                            [
                                                'data' => $properties,
                                            ]
                                        )
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endforeach
@endif
