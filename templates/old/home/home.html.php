<?php
$this->setLayout('layout/layout.html.php');
?>

<div class="min-h-screen bg-surface-alt p-8">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-heading">Kezuru Theme Test</h1>
        <p class="text-muted mt-1">Toggle dark mode to see both palettes. This page is temporary.</p>
    </div>

    <!-- Accent colours -->
    <div class="bg-surface rounded-lg border border-default p-6 mb-6">
        <h2 class="text-lg font-semibold text-heading mb-4">Accent Colours</h2>
        <div class="flex gap-3 flex-wrap">
            <span class="bg-primary text-inverse px-4 py-2 rounded-md text-sm font-medium">Primary</span>
            <span class="bg-primary-hover text-inverse px-4 py-2 rounded-md text-sm font-medium">Primary Hover</span>
            <span class="bg-primary-light text-primary px-4 py-2 rounded-md text-sm font-medium">Primary Light</span>
            <span class="bg-danger text-inverse px-4 py-2 rounded-md text-sm font-medium">Danger</span>
            <span class="bg-success text-inverse px-4 py-2 rounded-md text-sm font-medium">Success</span>
            <span class="bg-warning text-inverse px-4 py-2 rounded-md text-sm font-medium">Warning</span>
        </div>
    </div>

    <!-- Surfaces -->
    <div class="bg-surface rounded-lg border border-default p-6 mb-6">
        <h2 class="text-lg font-semibold text-heading mb-4">Surfaces</h2>
        <div class="flex gap-4 flex-wrap">
            <div class="bg-surface border border-default rounded-lg p-4 w-40 text-center">
                <span class="text-sm text-muted">surface</span>
            </div>
            <div class="bg-surface-alt border border-default rounded-lg p-4 w-40 text-center">
                <span class="text-sm text-muted">surface-alt</span>
            </div>
            <div class="bg-sidebar rounded-lg p-4 w-40 text-center">
                <span class="text-sm text-inverse">sidebar</span>
            </div>
        </div>
    </div>

    <!-- Typography -->
    <div class="bg-surface rounded-lg border border-default p-6 mb-6">
        <h2 class="text-lg font-semibold text-heading mb-4">Typography</h2>
        <p class="text-heading text-lg font-semibold">Heading text — text-heading</p>
        <p class="text-body mt-1">Body text — text-body. This is your default reading colour.</p>
        <p class="text-muted mt-1">Muted text — text-muted. For secondary information.</p>
    </div>

    <!-- Example card -->
    <div class="bg-surface rounded-lg border border-default p-6 mb-6">
        <h2 class="text-lg font-semibold text-heading mb-4">Example: Content Card</h2>
        <div class="bg-surface-alt rounded-lg border border-default p-5 max-w-md">
            <h3 class="text-heading font-semibold">Team Member</h3>
            <p class="text-muted text-sm mt-1">Last edited 2 hours ago</p>
            <div class="mt-4 flex gap-2">
                <button class="bg-primary text-inverse px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-hover">
                    Edit
                </button>
                <button class="bg-surface text-danger border border-default px-4 py-2 rounded-md text-sm font-medium">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Borders -->
    <div class="bg-surface rounded-lg border border-default p-6">
        <h2 class="text-lg font-semibold text-heading mb-4">Borders</h2>
        <div class="flex gap-4 flex-wrap">
            <div class="border border-default rounded-lg p-4 w-40 text-center">
                <span class="text-sm text-muted">default</span>
            </div>
            <div class="border border-strong rounded-lg p-4 w-40 text-center">
                <span class="text-sm text-muted">strong</span>
            </div>
        </div>
    </div>

</div>