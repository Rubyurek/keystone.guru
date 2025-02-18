<?php
/** @var boolean $isMobile */
/** @var string $id */
$id   = 'nitropay-' . $id;
$type = isset($type) ? $type : 'responsive';
$demo = config('app.env') !== 'production' ? 'true' : 'false';

$defaultReportAdPosition = [
    'responsive' => 'top-right',
    'header'     => 'bottom-right',
    'footer'     => 'top-right',
];

$reportAdPosition = isset($reportAdPosition) ? $reportAdPosition : $defaultReportAdPosition[$type];

// If we're on mobile, anchor ads do not have a report button so don't render it
$hasAdControls = !($isMobile && ($type === 'header' || $type === 'footer'));

if ($isMobile) {
    $id .= '_mobile';
}
?>
<script type="text/javascript">
    nitropayAdRenderedEvents['{{ $id }}'] = (event, count = 0) => {
        let adId = '{{ $id }}';

        // Fail safe just in case
        if (count > 5) {
            // console.log(`Broke out of too much checking for ${adId}`);
            return;
        }

        if (!nitropayIsAnchor.hasOwnProperty(adId) || !nitropayIsAnchor[adId]) {
            // Move the report ad button to a more convenient place
            let reportLink = document.getElementById(adId).getElementsByClassName('report-link')[0];

            if (typeof reportLink !== 'undefined') {
                // Show the remove ads button
                document.getElementById(`${adId}-remove-ads`).setAttribute('style', '');

                reportLink.setAttribute('style', '');
                reportLink.setAttribute('class', 'report-link nitropay');

                let aReportLink = reportLink.children[0];
                aReportLink.style['color'] = 'unset';
                aReportLink.style['margin-top'] = '0';

                let target = document.getElementById(`${adId}-report-ad`);
                // Clear any previously set report links
                target.innerHTML = '';
                target.appendChild(reportLink);
            } else {
                // console.log(`Ad ${adId} not found - retrying in 200ms`);
                setTimeout(nitropayAdRenderedEvents[adId], 200, event, ++count);
            }
        }
    };

    if (window.nitroAds && window.nitroAds.loaded) {
        // nitroAds was already loaded
    } else {
        // wait for loaded event
        document.addEventListener('nitroAds.rendered', nitropayAdRenderedEvents['{{ $id }}']);
    }
</script>

<div>
@if($hasAdControls && strpos($reportAdPosition, 'top') !== false)
    @include('common.thirdparty.nitropay.adcontrols', ['id' => $id])
@endif

@if( $type === 'responsive' )
    <!-- Responsive ad unit -->
        <div id="{{ $id }}" class="ad_block_me"></div>

        <script type="text/javascript">
            window['nitroAds'].createAd('{{ $id }}', {
                "refreshLimit": 10,
                "refreshTime": 30,

                "refreshVisibleOnly": true,
                "demo": {{$demo}},
                "report": {
                    "enabled": true,
                    "wording": "Report Ad",
                    "position": "{{ $reportAdPosition }}"
                }
            });
        </script>
@elseif( $type === 'header' )
    <!-- Top header ad unit -->
        <div id="{{ $id }}" class="ad_block_me"
             @if(!$isMobile)
             style="min-height: 90px;"
            @endif
        ></div>
        @if( $isMobile )
            <script type="text/javascript">
                window['nitroAds'].createAd('{{ $id }}', {
                    "refreshLimit": 10,
                    "refreshTime": 30,
                    "format": "anchor",
                    "anchor": "top",
                    "demo": {{$demo}},
                    "report": {
                        "enabled": true,
                        "wording": "Report Ad",
                        "position": "{{ $reportAdPosition }}"
                    }
                });
                nitropayIsAnchor['{{ $id }}'] = true;
            </script>
        @else
            <script type="text/javascript">
                window['nitroAds'].createAd('{{ $id }}', {
                    "refreshLimit": 10,
                    "refreshTime": 30,

                    "refreshVisibleOnly": true,
                    "demo": {{$demo}},
                    "sizes": [
                        [
                            "{{ isset($map) && $map ? 728 : 970}}",
                            "90"
                        ]
                    ],
                    "report": {
                        "enabled": true,
                        "wording": "Report Ad",
                        "position": "{{ $id }}"
                    }
                });
            </script>
        @endif
    @elseif( $type === 'footer' )
    <!-- Footer ad unit -->
        @php($height = isset($map) && $map ? 90 : 250)
        <div id="{{ $id }}" class="ad_block_me"
             @if(!$isMobile)
             style="min-height: {{ $height }}px;"
            @endif
        ></div>

        @if( $isMobile )
            <script type="text/javascript">
                window['nitroAds'].createAd('{{ $id }}', {
                    "refreshLimit": 10,
                    "refreshTime": 30,
                    "demo": {{$demo}},
                    "format": "anchor",
                    "anchor": "bottom",
                    "report": {
                        "enabled": true,
                        "wording": "Report Ad",
                        "position": "{{ $reportAdPosition }}"
                    },
                    "mediaQuery": "(min-width: 320px) and (max-width: 767px)"
                });
                nitropayIsAnchor['{{ $id }}'] = true;
            </script>
        @else
            <script type="text/javascript">
                window['nitroAds'].createAd('{{ $id }}', {
                    "refreshLimit": 10,
                    "refreshTime": 30,

                    "refreshVisibleOnly": true,
                    "demo": {{$demo}},
                    "sizes": [
                        [
                            "{{ isset($map) && $map ? 728 : 970 }}",
                            "{{ $height }}"
                        ]
                    ],
                    "report": {
                        "enabled": true,
                        "wording": "Report Ad",
                        "position": "{{ $reportAdPosition }}"
                    },
                    "mediaQuery": "(min-width: 1025px), (min-width: 768px) and (max-width: 1024px)"
                });
            </script>
        @endif
    @endif
</div>

@if($hasAdControls && strpos($reportAdPosition, 'bottom') !== false)
    @include('common.thirdparty.nitropay.adcontrols', ['id' => $id])
@endif
