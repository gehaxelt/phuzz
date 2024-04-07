class ScoringFormula():
    def calculate_score(self, candidate):
        pass
    def calculate_priority(self, candidate):
        pass
    def calculate_energy(self, candidate):
        pass


class DefaultScoringFormula(ScoringFormula):
    def calculate_score(self, candidate):
        hit_counter=0
        for path in candidate.new_paths:
            filename, lines = path.split('::::')
            hit_counter += lines.count("_")

        return hit_counter + len(candidate.paths)

    def calculate_priority(self, candidate):
        return self.calculate_score(candidate)

    def calculate_energy(self, candidate):
        if candidate.parent is not None:
            energy = max(1, candidate.parent.number_of_new_paths + abs(candidate.parent.score - candidate.score))
        else:
            energy = max(1, len(candidate.new_paths))
        return energy 
