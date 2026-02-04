# Real-time Engagement Moodle Plugin

## Overview

The Real-time Engagement plugin for Moodle 4.3+ provides teachers and administrators with live analytics of student engagement within their courses. This powerful tool tracks student activity across various Moodle components like quizzes, forums, and lesson pages, offering immediate insights into participation and attention levels.

## Features

*   **Live Engagement Dashboard**: A dynamic, real-time dashboard displaying student participation and attention. Teachers can see at a glance who is active and who might be disengaging.
*   **Engagement Scoring**: Sophisticated scoring mechanism based on:
    *   Quiz attempts
    *   Forum posts and discussions
    *   Lesson page views and interactions
    *   Video content interactions
*   **AI-driven Disengagement Alerts**: Automatically identifies students who fall below configurable engagement thresholds and sends alerts to teachers, enabling timely intervention.
*   **Configurable Settings**: Administrators can customize:
    *   Dashboard refresh intervals
    *   Weighting for different activity types in the engagement score calculation
    *   Disengagement thresholds
    *   Notification preferences for AI alerts
*   **Role-based Capabilities**: Ensures that only authorized users (teachers, managers) can view engagement dashboards and manage settings, while student activity is tracked silently.
*   **Database Storage**: Dedicated database tables to store granular engagement events and calculated scores, providing a foundation for both real-time display and future historical analysis.
*   **Moodle Standards Compliant**: Built with Moodle's security, coding, and privacy standards in mind, ensuring a robust and reliable integration.

## Installation

1.  **Download**: Download the plugin ZIP file.
2.  **Unzip**: Unzip the contents into the `local` directory of your Moodle installation (`moodle/local/`). Ensure the folder name is `realtime_engagement`.
3.  **Navigate to Notifications**: Log in to your Moodle site as an administrator and go to `Site administration > Notifications`. Moodle will detect the new plugin and prompt you to install it.
4.  **Follow On-Screen Instructions**: Complete the installation process by following the prompts. Moodle will set up the necessary database tables and capabilities.
5.  **Configure Settings**: After installation, navigate to `Site administration > Plugins > Local plugins > Real-time Engagement` to configure the plugin's settings, such as refresh intervals, scoring weights, and alert thresholds.

## Configuration Guide

After installation, you can access the plugin settings via `Site administration > Plugins > Local plugins > Real-time Engagement`.

*   **Dashboard Refresh Interval (seconds)**: Set how often the live dashboard refreshes. A lower number provides more real-time data but may increase server load. Default: 30 seconds.
*   **Engagement Scoring Weights**: Adjust the importance of different activity types in calculating the overall engagement score. Higher values for an activity mean it contributes more to the score. Ensure the sum of weights reflects your pedagogical priorities.
    *   **Quiz Activity Weight**
    *   **Forum Activity Weight**
    *   **Lesson Activity Weight**
    *   **Video Interaction Weight**
*   **Disengagement Threshold**: Define the engagement score (0-100) below which a student is considered disengaged. When a student's score drops below this, an AI alert can be triggered.
*   **Enable AI Alert Notifications**: Check this box to enable automatic notifications to teachers when students are identified as disengaged. This helps teachers proactively support students.

## Usage Examples

### For Teachers

1.  **Accessing the Dashboard**: Navigate to any course where you are an editing teacher. In the course navigation menu (usually on the left), you will find a link titled 