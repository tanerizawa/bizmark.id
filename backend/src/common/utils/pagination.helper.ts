import { SelectQueryBuilder } from 'typeorm';
import { PaginatedResponse, QueryOptions } from '../interfaces/common.interface';

export class PaginationHelper {
  static async paginate<T>(
    queryBuilder: SelectQueryBuilder<T>,
    options: QueryOptions,
  ): Promise<PaginatedResponse<T>> {
    const page = Math.max(options.page || 1, 1);
    const limit = Math.min(Math.max(options.limit || 10, 1), 100);
    const skip = (page - 1) * limit;

    // Apply sorting
    if (options.sortBy) {
      const order = options.sortOrder === 'ASC' ? 'ASC' : 'DESC';
      queryBuilder.orderBy(`${queryBuilder.alias}.${options.sortBy}`, order);
    }

    // Apply pagination
    queryBuilder.skip(skip).take(limit);

    const [data, total] = await queryBuilder.getManyAndCount();

    const totalPages = Math.ceil(total / limit);
    const hasNext = page < totalPages;
    const hasPrev = page > 1;

    return {
      data,
      meta: {
        page,
        limit,
        total,
        totalPages,
        hasNext,
        hasPrev,
      },
    };
  }

  static buildSearchQuery<T>(
    queryBuilder: SelectQueryBuilder<T>,
    searchTerm: string,
    searchFields: string[],
  ): SelectQueryBuilder<T> {
    if (!searchTerm || !searchFields.length) {
      return queryBuilder;
    }

    const conditions = searchFields.map(
      (field, index) => `${queryBuilder.alias}.${field} ILIKE :search${index}`,
    );

    queryBuilder.andWhere(
      `(${conditions.join(' OR ')})`,
      searchFields.reduce(
        (params, _, index) => ({
          ...params,
          [`search${index}`]: `%${searchTerm}%`,
        }),
        {},
      ),
    );

    return queryBuilder;
  }
}
