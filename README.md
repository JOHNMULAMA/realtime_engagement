# Real-time Engagement Moodle Plugin

## Overview

The Real-time Engagement plugin for Moodle 4.3+ provides teachers and administrators with live analytics of student engagement within their courses. This powerful tool tracks student activity across various Moodle components like quizzes, forums, lesson pages, and video resources, offering immediate insights into participation and attention levels.

## Features

* **Live Engagement Dashboard**: A dynamic, real-time dashboard displaying student participation and attention. Teachers can see at a glance who is active and who might be disengaging.
* **Engagement Scoring**: Sophisticated scoring mechanism based on:
    * Quiz attempts
    * Forum posts and discussions
    * Lesson page views and interactions
    * Video content interactions
* **AI-driven Disengagement Alerts**: Automatically identifies students who fall below configurable engagement thresholds and sends alerts to teachers, enabling timely intervention.
* **Configurable Settings**: Administrators can customize:
    * Dashboard refresh intervals
    * Weighting for different activity types in the engagement score calculation
    * Disengagement thresholds
    * Notification preferences for AI alerts
* **Role-based Capabilities**: Ensures that only authorized users (teachers, managers) can view engagement dashboards and manage settings, while student activity is tracked silently.
* **Database Storage**: Dedicated database tables to store granular engagement events and calculated scores, providing a foundation for both real-time display and future historical analysis.
* **Moodle Standards Compliant**: Built with Moodle's security, coding, and privacy standards in mind, ensuring a robust and reliable integration.

## Installation

1. **Download**: Download the plugin ZIP file.
2. **Unzip**: Unzip the contents into the `local` directory of your Moodle installation (`moodle/local/`). Ensure the folder name is `realtime_engagement`.
3. **Navigate to Notifications**: Log in to your Moodle site as an administrator and go to `Site administration > Notifications`. Moodle will detect the new plugin and prompt you to install it.
4. **Follow On-Screen Instructions**: Complete the installation process by following the prompts. Moodle will set up the necessary database tables and capabilities.
5. **Configure Settings**: After installation, navigate to `Site administration > Plugins > Local plugins > Real-time Engagement` to configure the plugin's settings, such as refresh intervals, scoring weights, and alert thresholds.

## Configuration Guide

After installation, you can access the plugin settings via `Site administration > Plugins > Local plugins > Real-time Engagement`.

* **Dashboard Refresh Interval (seconds)**: Set how often the live dashboard refreshes. A lower number provides more real-time data but may increase server load. Default: 30 seconds.
* **Engagement Scoring Weights**: Adjust the importance of different activity types in calculating the overall engagement score. Higher values for an activity mean it contributes more to the score. Ensure the sum of weights reflects your pedagogical priorities.
    * **Quiz Activity Weight**
    * **Forum Activity Weight**
    * **Lesson Activity Weight**
    * **Video Interaction Weight**
* **Disengagement Threshold**: Define the engagement score (0-100) below which a student is considered disengaged. When a student's score drops below this, an AI alert can be triggered.
* **Enable AI Alert Notifications**: Check this box to enable automatic notifications to teachers when students are identified as disengaged.

## Usage Examples

### For Teachers

1. **Accessing the Dashboard**: Navigate to any course where you are an editing teacher. In the course navigation menu, you will find a link titled **Real-time Engagement Dashboard**.
2. **Monitor Engagement**: View the live dashboard to see which students are actively participating and which may be disengaged.
3. **Receive AI Alerts**: If a student’s engagement drops below the threshold, notifications are sent automatically to your Moodle messaging interface and optionally via email.
4. **Adjust Settings**: Administrators or teachers with capability can adjust weights, thresholds, and refresh intervals to align engagement monitoring with course priorities.

### For Students

* Student activity is tracked silently — no direct interface is visible to students.
* Their engagement score is automatically calculated based on activity in quizzes, forums, lessons, and videos.

## Capabilities

* `local/realtime_engagement:viewdashboard` – Allows viewing of the real-time engagement dashboard.
* `local/realtime_engagement:trackevents` – Used internally to track student events (no direct user interface).
* Role-based access ensures only authorized roles (teachers, managers) can access dashboards and settings.

## Database

The plugin creates two main tables:

1. `local_realtime_engagement_events` – Stores individual engagement events.
2. `local_realtime_engagement_scores` – Stores calculated engagement scores per user per course.

All tables are properly indexed and foreign-keyed to Moodle core tables (`user` and `course`) for data integrity.

## Troubleshooting

* **“Too few arguments” error**: Ensure `lib.php` is updated to the latest Moodle 4+ compatible version.
* **Engagement scores not updating**: Confirm that event tracking is enabled and cron jobs are running.
* **Notifications not sent**: Verify AI alert notifications are enabled in plugin settings and that teachers have messaging capabilities.

## Version History

* **1.0.0** – Initial stable release for Moodle 4.3+.
* **1.1.0** – Fixed navigation function, event tracking enhancements, AI alert improvements.
* **1.2.0** – Full Moodle 4+ compatible `lib.php`, database fixes, secure notifications, refactored scoring.

## License

GNU GPL v3 or later. See [LICENSE](http://www.gnu.org/copyleft/gpl.html) for details.

## Support

For support or feature requests, please contact **John Mulama** at `johnmulama001@gmail.com`.
