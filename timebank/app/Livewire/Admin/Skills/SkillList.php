<?php

namespace App\Livewire\Admin\Skills;

use App\Models\Skill;
use Livewire\Component;
use Livewire\WithPagination;

class SkillList extends Component
{
    use WithPagination;

    public $name = '';
    public $search = '';
    public $editingSkillId = null;
    public $editingSkillName = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255|unique:skills,name' . ($this->editingSkillId ? ',' . $this->editingSkillId : ''),
            'editingSkillName' => 'required|string|min:2|max:255|unique:skills,name,' . $this->editingSkillId,
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function saveSkill()
    {
        $this->validateOnly('name');

        Skill::create(['name' => $this->name]);

        $this->reset('name');
        session()->flash('message', 'Skill added successfully.');
        $this->dispatch('skill-added');
    }

    public function editSkill(Skill $skill)
    {
        $this->editingSkillId = $skill->id;
        $this->editingSkillName = $skill->name;
    }

    public function updateSkill()
    {
        $this->validateOnly('editingSkillName');

        if ($this->editingSkillId) {
            $skill = Skill::find($this->editingSkillId);
            if ($skill) {
                $skill->update(['name' => $this->editingSkillName]);
                session()->flash('message', 'Skill updated successfully.');
            }
        }
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->reset(['editingSkillId', 'editingSkillName']);
    }

    public function deleteSkill(Skill $skill)
    {
        // Check if skill is in use before deleting (optional, good practice)
        if ($skill->users()->count() > 0) {
            session()->flash('error', 'Cannot delete skill. It is currently assigned to one or more users.');
            return;
        }
        // Add similar checks for service_requests if required_skills uses skill IDs directly and needs integrity

        $skill->delete();
        session()->flash('message', 'Skill deleted successfully.');
    }

    public function render()
    {
        $skills = Skill::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.skills.skill-list', [
            'skills' => $skills,
        ])->layout('layouts.app');
    }
}
