@extends('layouts/layoutMaster')

@section('title', 'Workforce Management Dashboard')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss', 'resources/assets/vendor/libs/swiper/swiper.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/cards-advance.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/swiper/swiper.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // KPI Cards Animation
            const kpiCards = document.querySelectorAll('.kpi-counter');
            kpiCards.forEach(card => {
                const target = parseInt(card.getAttribute('data-target'), 10);
                let count = 0;
                const duration = 2000; // 2 seconds
                const increment = Math.ceil(target / (duration / 16)); // 60fps

                const updateCount = () => {
                    const current = parseInt(card.innerText, 10);
                    if (current < target) {
                        card.innerText = Math.min(current + increment, target);
                        setTimeout(updateCount, 16);
                    }
                };

                updateCount();
            });

            // User Registration Chart
            @if(isset($userRegistrationTrend) && count($userRegistrationTrend) > 0)
            const userTrendElement = document.querySelector('#userRegistrationChart');
            if (userTrendElement) {
                const userTrendOptions = {
                    series: [{
                        name: 'New Users',
                        data: [
                            @foreach($userRegistrationTrend as $trend)
                                {{ $trend['total'] }},
                            @endforeach
                        ]
                    }],
                    chart: {
                        height: 300,
                        type: 'area',
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    colors: ['#696cff'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: [
                            @foreach($userRegistrationTrend as $trend)
                                "{{ $trend['month'] }}",
                            @endforeach
                        ],
                        labels: {
                            rotate: -45,
                            rotateAlways: true
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Users'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + " users"
                            }
                        }
                    }
                };
                
                const userTrendChart = new ApexCharts(userTrendElement, userTrendOptions);
                userTrendChart.render();
            }
            @endif

            // Application Status Chart (Donut)
            @if(isset($applicationStats) && count($applicationStats) > 0)
            const applicationStatusElement = document.querySelector('#applicationStatusChart');
            if (applicationStatusElement) {
                const applicationOptions = {
                    series: [
                        {{ $applicationStats['approved'] ?? 0 }},
                        {{ $applicationStats['pending'] ?? 0 }},
                        {{ $applicationStats['rejected'] ?? 0 }},
                        {{ $applicationStats['completed'] ?? 0 }}
                    ],
                    chart: {
                        width: '100%',
                        type: 'donut',
                    },
                    labels: ['Approved', 'Pending', 'Rejected', 'Completed'],
                    colors: ['#71dd37', '#ffab00', '#ff3e1d', '#03c3ec'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return Math.round(val) + '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                        }
                                    }
                                }
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 320
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };
                
                const applicationStatusChart = new ApexCharts(applicationStatusElement, applicationOptions);
                applicationStatusChart.render();
            }
            @endif

            // Gender Distribution Chart
            @if(isset($genderDistribution) && count($genderDistribution) > 0)
            const genderDistElement = document.querySelector('#genderDistributionChart');
            if (genderDistElement) {
                const genderOptions = {
                    series: [
                        {{ $genderDistribution['male'] ?? 0 }},
                        {{ $genderDistribution['female'] ?? 0 }}
                    ],
                    chart: {
                        type: 'pie',
                        width: '100%',
                    },
                    labels: ['Male', 'Female'],
                    colors: ['#696cff', '#ff6c9c'],
                    dataLabels: {
                        enabled: true
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 320
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };
                
                const genderChart = new ApexCharts(genderDistElement, genderOptions);
                genderChart.render();
            }
            @endif

            // Monthly Performance Chart
            @if(isset($monthlyPerformance) && count($monthlyPerformance) > 0)
            const performanceElement = document.querySelector('#monthlyPerformanceChart');
            if (performanceElement) {
                const performanceOptions = {
                    series: [{
                        name: 'Events',
                        type: 'column',
                        data: [
                            @foreach($monthlyPerformance as $perf)
                                {{ $perf['events'] }},
                            @endforeach
                        ]
                    }, {
                        name: 'Applications',
                        type: 'column',
                        data: [
                            @foreach($monthlyPerformance as $perf)
                                {{ $perf['applications'] }},
                            @endforeach
                        ]
                    }, {
                        name: 'Hours Worked',
                        type: 'line',
                        data: [
                            @foreach($monthlyPerformance as $perf)
                                {{ $perf['hours_worked'] }},
                            @endforeach
                        ]
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false,
                        toolbar: {
                            show: false
                        }
                    },
                    stroke: {
                        width: [0, 0, 3]
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '50%'
                        }
                    },
                    colors: ['#696cff', '#03c3ec', '#ff6c9c'],
                    fill: {
                        opacity: [0.85, 0.85, 1],
                    },
                    xaxis: {
                        categories: [
                            @foreach($monthlyPerformance as $perf)
                                "{{ $perf['month'] }}",
                            @endforeach
                        ],
                    },
                    yaxis: [
                        {
                            seriesName: 'Events',
                            title: {
                                text: 'Events & Applications',
                            },
                            min: 0
                        },
                        {
                            seriesName: 'Hours Worked',
                            opposite: true,
                            title: {
                                text: 'Hours Worked'
                            },
                            min: 0
                        }
                    ],
                    tooltip: {
                        shared: true,
                        intersect: false
                    }
                };
                
                const performanceChart = new ApexCharts(performanceElement, performanceOptions);
                performanceChart.render();
            }
            @endif

            // Weekly Attendance Chart
            @if(isset($weeklyAttendance) && count($weeklyAttendance) > 0)
            const attendanceElement = document.querySelector('#weeklyAttendanceChart');
            if (attendanceElement) {
                const attendanceOptions = {
                    series: [{
                        name: 'Check-ins',
                        data: [
                            @foreach($weeklyAttendance as $record)
                                {{ $record['check_ins'] }},
                            @endforeach
                        ]
                    }],
                    chart: {
                        height: 280,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '30%',
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: [
                            @foreach($weeklyAttendance as $record)
                                "{{ $record['date'] }}",
                            @endforeach
                        ],
                        labels: {
                            rotate: -45,
                            rotateAlways: true
                        }
                    },
                    colors: ['#03c3ec'],
                    yaxis: {
                        title: {
                            text: 'Number of Check-ins'
                        }
                    }
                };
                
                const attendanceChart = new ApexCharts(attendanceElement, attendanceOptions);
                attendanceChart.render();
            }
            @endif

            // Top Positions Chart
            @if(isset($topPositions) && count($topPositions) > 0)
            const topPositionsElement = document.querySelector('#topPositionsChart');
            if (topPositionsElement) {
                const topPositionsOptions = {
                    series: [{
                        name: 'Applications',
                        data: [
                            @foreach($topPositions as $position)
                                {{ $position['application_count'] }},
                            @endforeach
                        ]
                    }],
                    chart: {
                        type: 'bar',
                        height: 280,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            columnWidth: '55%',
                            borderRadius: 4,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: [
                            @foreach($topPositions as $position)
                                "{{ $position['title'] }}",
                            @endforeach
                        ],
                    },
                    colors: ['#71dd37'],
                    yaxis: {
                        title: {
                            text: 'Number of Applications'
                        }
                    }
                };
                
                const topPositionsChart = new ApexCharts(topPositionsElement, topPositionsOptions);
                topPositionsChart.render();
            }
            @endif
        });
    </script>
@endsection

@section('content')
    @if(isset($error))
        <div class="alert alert-danger">
            <p>An error occurred: {{ $error }}</p>
            <p>Please try refreshing the page or contact technical support.</p>
        </div>
    @else
        <!-- Dashboard Analytics -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Workforce Management Dashboard</h5>
                            <small class="text-muted">System overview and key performance indicators</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dashboardActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti ti-dots-vertical me-1"></i> Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dashboardActions">
                                <a class="dropdown-item" href="javascript:void(0);">Refresh Data</a>
                                <a class="dropdown-item" href="javascript:void(0);">Export Report</a>
                                <a class="dropdown-item" href="javascript:void(0);">Print Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row">
            <!-- Users Card -->
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text mb-1">Total Users</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2 kpi-counter" data-target="{{ $totalUsers }}">0</h4>
                                </div>
                                <small>{{ $totalSupervisors }} supervisors registered</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="ti ti-users ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

       
     
       
        </div>

      
       
    @endif
@endsection
