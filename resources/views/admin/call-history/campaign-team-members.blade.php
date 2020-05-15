<option value="">@lang('module_lead.selectTeamMember')</option>
@forelse($campaignTeamMembers as $campaignTeamMember)
    <option value="{{ $campaignTeamMember->user->id }}">{{ $campaignTeamMember->user->name }}</option>
@empty

@endforelse