@extends( 'template' )
@section( 'content' )

    <div class="row">
        <div class="col-md-12 leaderboard-results">
            <div class="showing-x-results">
                @if( isset($page) )
                    The Top {{ ( $page - 1 ) * $per_page }} - {{ ( $page ) * $per_page }} of {{ $count }}
                @else
                    The Top {{ $per_page }} of {{ $count }}
                @endif
            </div>
            @if( $top100 )
                <table class="table sortable">
                    <thead>
                        <tr>
                            <th data-defaultsort="disabled"></th>
                            <th class="name" data-defaultsort="disabled">Who</th>
                            <th data-firstsort="desc">Rides</th>
                            <th data-firstsort="desc">Miles</th>
                            <th data-firstsort="desc">Riding Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $count = isset($page) ? ( $page - 1 ) * $per_page : 0; ?>
                    @foreach( $top100 as $entry )
                        <?php $count++; ?>
                        <tr>
                            <td>
                                #{{ $count }}
                            </td>
                            <td class="name">
                                <a href="{{ action('PagesController@home') . '?utid=' . $entry->utid }}">
                                    <img src="{{ $entry->photo }}" class="user-photo" width="25" />
                                    @if( $session && $entry->utid == $session['utid'] )
                                        <span class="leaderboard-you">You!</span>
                                    @else
                                        {{ $entry->name }}
                                    @endif
                                </a>
                            </td>
                            <td data-value="{{ $entry->rides_count }}">
                                {{ number_format( $entry->rides_count ) }}
                            </td>
                            <td data-value="{{ $entry->miles_driven }}">
                                {{ number_format( $entry->miles_driven, 2 ) }}
                            </td>
                            <td data-value="{{ $entry->total_time }}">
                                {{ display_seconds_pretty( $entry->total_time ) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagin">
                    @if( isset( $page ) )
                        @if( ( floor($count / $per_page) ) > ($page - 1) )
                            <a href="?page={{ $page + 1 }}&per_page={{ $per_page }}">Next {{ $per_page }}</a>
                        @endif
                    @else
                        <a href="?page=2&per_page={{ $per_page }}">Next {{ $per_page }}</a>
                    @endif

                    @if( $per_page == 100 )
                        <a href="{{ action('PagesController@leaderboard') }}?per_page=500" style="display:block;padding-top: 10px;font-size: 12px;font-style:italic;">500 Per Page</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@stop